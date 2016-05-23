<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Functional\Entity;

use Doctrine\ORM\EntityManager;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\WorkflowBundle\Model\WorkflowManager;

class IssueRepositoryTest extends WebTestCase
{
    /**
     * @var array
     */
    private $demoIssues;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var WorkflowManager
     */
    private $workflowManager;

    protected function setUp()
    {
        $this->initClient([ ], $this->generateWsseAuthHeader());

        $demoIssuesFile = __DIR__ . '/../../../Resources/fixtures/demo-issues.json';

        $this->demoIssues = json_decode(file_get_contents($demoIssuesFile), true);
        // take only 3
        $this->demoIssues = array_slice($this->demoIssues, 0, 3);

        $this->manager = $this->getContainer()
                              ->get('doctrine');

        $this->workflowManager = $this->getWorkflowManager();
    }

    public function testGetIssuesByStatus()
    {
        $histogram = $workflowLabels = $insertedIds = [ ];

        $issueRepo = $this->manager
            ->getRepository('OroAcademyIssueBundle:Issue');

        foreach ($this->demoIssues as $demoIssue) {
            if ('subtask' === $demoIssue['type']) {
                $this->client->request(
                    'POST',
                    $this->getUrl('oroacademy_api_post_issue'),
                    [ 'subtask' => $demoIssue ]
                );
            } else {
                $this->client->request(
                    'POST',
                    $this->getUrl('oroacademy_api_post_issue'),
                    [ 'issue' => $demoIssue ]
                );
            }
            $result = $this->getJsonResponseContent($this->client->getResponse(), 201);
            $this->assertArrayHasKey('id', $result);
            $issue      = $issueRepo->find($result['id']);

            // Update the workflow randomly just by one step to keep it simple.
            $wfItem     = $this->workflowManager->getWorkflowItemByEntity($issue);
            $randomStep = 'Open';
            if (rand(0, 1) === 1) {
                $randomStep = 'In Progress';
                $this->workflowManager->transit($wfItem, 'start_progress');
            }

            if (!isset($histogram[$randomStep])) {
                $histogram[$randomStep] = 0;
            }
            $histogram[$randomStep]++;

            $insertedIds[] = $result['id'];
        }

        $issueRepo = $this->manager
            ->getRepository('OroAcademyIssueBundle:Issue');

        $result = $issueRepo->getIssuesByStatus();

        // Compare the workflow step histogram with custom data.
        // Keep in mind there could be other Issues in the database yet.
        foreach ($result as $resultSet) {
            $workflowLabel = $resultSet['label'];
            if (isset($histogram[$workflowLabel])) {
                $this->assertGreaterThanOrEqual($histogram[$workflowLabel], $resultSet['number']);
            }
        }

        // Remove previously added Issues.
        foreach ($insertedIds as $id) {
            $this->client->request(
                'DELETE',
                $this->getUrl('oroacademy_api_delete_issue', [ 'id' => $id ])

            );
        }
    }

    /**
     * @return WorkflowManager
     */
    protected function getWorkflowManager()
    {
        return $this->client->getContainer()->get('oro_workflow.manager');
    }
}