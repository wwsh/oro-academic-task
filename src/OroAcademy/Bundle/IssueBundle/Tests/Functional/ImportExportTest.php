<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Functional;

use Oro\Bundle\ImportExportBundle\Job\JobExecutor;
use Oro\Bundle\ImportExportBundle\Processor\ProcessorRegistry;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Akeneo\Bundle\BatchBundle\Job\DoctrineJobRepository as BatchJobRepository;
use Symfony\Component\DomCrawler\Form;

class ImportExportTest extends WebTestCase
{
    /**
     * @var string
     */
    protected $file;

    protected function setUp()
    {
        $this->initClient([ ], $this->generateBasicAuthHeader());
    }

    /**
     * Delete data required because there is commit to job repository in import/export controller action
     * Please use
     *   $this->getContainer()->get('akeneo_batch.job_repository')->getJobManager()->beginTransaction();
     *   $this->getContainer()->get('akeneo_batch.job_repository')->getJobManager()->rollback();
     *   $this->getContainer()->get('akeneo_batch.job_repository')->getJobManager()->getConnection()->clear();
     * if you don't use controller
     */
    protected function tearDown()
    {
        // clear DB from separate connection, close to avoid connection limit and memory leak
        $batchJobManager = $this->getBatchJobManager();
        $batchJobManager->createQuery('DELETE AkeneoBatchBundle:JobInstance')->execute();
        $batchJobManager->createQuery('DELETE AkeneoBatchBundle:JobExecution')->execute();
        $batchJobManager->createQuery('DELETE AkeneoBatchBundle:StepExecution')->execute();

        parent::tearDown();
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getBatchJobManager()
    {
        /**
         * @var BatchJobRepository $batchJobRepository
         */
        $batchJobRepository = $this->getContainer()->get('akeneo_batch.job_repository');
        return $batchJobRepository->getJobManager();
    }

    public function strategyDataProvider()
    {
        return [
            'add or replace' => [ 'oroacademy_issue.add_or_replace' ],
        ];
    }

    /**
     * @param string $strategy
     * @dataProvider strategyDataProvider
     */
    public function testImportExport($strategy)
    {
        $this->validateImportFile($strategy);
        $this->doImport($strategy);

        $this->doExport();
        $this->validateExportResult();
    }

    /**
     * @param string $strategy
     */
    protected function validateImportFile($strategy)
    {
        $crawler = $this->client->request(
            'GET',
            $this->getUrl(
                'oro_importexport_import_form',
                [
                    'entity'           => 'OroAcademy\Bundle\IssueBundle\Entity\Issue',
                    '_widgetContainer' => 'dialog'
                ]
            )
        );
        $result  = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        $this->file = $this->getImportTemplate();
        $this->assertTrue(file_exists($this->file));

        /**
         * @var Form $form
         */
        $form = $crawler->selectButton('Submit')->form();

        /**
         * TODO Change after BAP-1813
         */
        $form->getFormNode()->setAttribute(
            'action',
            $form->getFormNode()->getAttribute('action') . '&_widgetContainer=dialog'
        );

        $form['oro_importexport_import[file]']->upload($this->file);
        $form['oro_importexport_import[processorAlias]'] = $strategy;

        $this->client->followRedirects(true);
        $this->client->submit($form);

        $result = $this->client->getResponse();

        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        $crawler = $this->client->getCrawler();
        $this->assertEquals(0, $crawler->filter('.import-errors')->count());
    }

    /**
     * @param string $strategy
     */
    protected function doImport($strategy)
    {
        // test import
        $this->client->followRedirects(false);
        $this->client->request(
            'GET',
            $this->getUrl(
                'oro_importexport_import_process',
                [
                    'processorAlias' => $strategy,
                    '_format'        => 'json'
                ]
            )
        );

        $data = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertEquals(
            [
                'success'   => true,
                'message'   => 'File was successfully imported.',
                'errorsUrl' => null
            ],
            $data
        );
    }

    protected function doExport()
    {
        $this->client->followRedirects(false);
        $this->client->request(
            'GET',
            $this->getUrl(
                'oro_importexport_export_instant',
                [
                    'processorAlias' => 'oroacademy_issue',
                    '_format'        => 'json'
                ]
            )
        );

        $data = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertTrue($data['success']);
        $this->assertGreaterThan(0, $data['readsCount']);
        $this->assertEquals(0, $data['errorsCount']);

        $this->client->request(
            'GET',
            $data['url']
        );

        $result = $this->client->getResponse();
        $this->assertResponseStatusCodeEquals($result, 200);
        $this->assertResponseContentTypeEquals($result, 'text/csv');
    }

    protected function validateExportResult()
    {
        $importTemplate = $this->getFileContents($this->file);
        $exportedData   = $this->getFileContents($this->getExportFile());

        $this->assertNotEmpty($importTemplate);
        $this->assertNotEmpty($exportedData);

        $commonFields = array_intersect($importTemplate[0], $exportedData[0]);
        // updatedAt is an auto updated field and may interfere
        // with stability of the results
        if (($index = array_search('Updated At', $commonFields)) !== false) {
            unset($commonFields[$index]);
        }
        // createdAt - same problem
        if (($index = array_search('Created At', $commonFields)) !== false) {
            unset($commonFields[$index]);
        }
        $importTemplate = $this->prepareImportTemplate($importTemplate, $commonFields);
        $exportedData   = $this->prepareExportedData($exportedData, $commonFields);

        $this->assertMatchingAtLeastOneItemInExportedData($exportedData, $importTemplate);
    }

    /**
     * @return string
     */
    protected function getImportTemplate()
    {
        $result = $this
            ->getContainer()
            ->get('oro_importexport.handler.export')
            ->getExportResult(
                JobExecutor::JOB_EXPORT_TEMPLATE_TO_CSV,
                'oroacademy_issue',
                ProcessorRegistry::TYPE_EXPORT_TEMPLATE
            );

        $chains = explode('/', $result['url']);
        return $this
            ->getContainer()
            ->get('oro_importexport.file.file_system_operator')
            ->getTemporaryFile(end($chains))
            ->getRealPath();
    }

    /**
     * @return string
     */
    protected function getExportFile()
    {
        $result = $this
            ->getContainer()
            ->get('oro_importexport.handler.export')
            ->handleExport(
                JobExecutor::JOB_EXPORT_TO_CSV,
                'oroacademy_issue',
                ProcessorRegistry::TYPE_EXPORT
            );

        $result = json_decode($result->getContent(), true);
        $chains = explode('/', $result['url']);
        return $this
            ->getContainer()
            ->get('oro_importexport.file.file_system_operator')
            ->getTemporaryFile(end($chains))
            ->getRealPath();
    }

    /**
     * @param string $fileName
     * @return array
     */
    protected function getFileContents($fileName)
    {
        $content = file_get_contents($fileName);
        $content = explode("\n", $content);
        $content = array_filter($content, 'strlen');
        return array_map('str_getcsv', $content);
    }

    /**
     * Nicely key all elements in the exportedData set.
     *
     * @param $exportedData
     * @param $commonFields
     */
    protected function prepareExportedData($exportedData, $commonFields)
    {
        $commonFieldsAsKeys = array_flip($commonFields);

        foreach ($exportedData as $rowId => $exportedDataRow) {
            if (!$rowId) {
                continue;
            }

            $newDataRow = array_combine(
                $exportedData[0],
                $exportedDataRow
            );

            $exportedData[$rowId] = array_intersect_key(
                $newDataRow,
                $commonFieldsAsKeys
            );
        }

        return $exportedData;
    }

    /**
     * @param $importTemplate
     * @param $commonFields
     * @return array
     */
    protected function prepareImportTemplate($importTemplate, $commonFields)
    {
        $importTemplate = array_combine($importTemplate[0], $importTemplate[1]);

        $commonFields = array_flip($commonFields);

        $importTemplate = array_intersect_key($importTemplate, $commonFields);

        return $importTemplate;
    }

    /**
     * @param $exportedData
     * @param $importTemplate
     */
    protected function assertMatchingAtLeastOneItemInExportedData(
        $exportedData,
        $importTemplate
    ) {
        $hasMatched = false;

        foreach ($exportedData as $exportedDataRow) {
            if (!isset($exportedDataRow['Code'])) {
                continue;
            }
            if ($exportedDataRow['Code'] === $importTemplate['Code']) {
                $this->assertArrayIntersectEquals($importTemplate, $exportedDataRow);
                $hasMatched = true;
            }
        }

        $this->assertTrue($hasMatched, 'No exported set, matching imported data, found');
    }
}
