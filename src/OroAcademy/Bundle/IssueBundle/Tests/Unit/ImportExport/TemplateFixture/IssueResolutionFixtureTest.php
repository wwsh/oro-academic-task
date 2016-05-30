<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\ImportExport\TemplateFixture;

use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateEntityRegistry;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateManager;
use OroAcademy\Bundle\IssueBundle\Entity\IssueResolution;
use OroAcademy\Bundle\IssueBundle\ImportExport\TemplateFixture\IssueResolutionFixture;

class IssueResolutionFixtureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IssueResolutionFixture
     */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new IssueResolutionFixture();
    }

    public function testGetEntityClass()
    {
        $this->assertEquals('OroAcademy\Bundle\IssueBundle\Entity\IssueResolution', $this->fixture->getEntityClass());
    }

    public function testCreateEntity()
    {
        $this->fixture->setTemplateManager($this->getTemplateManager());

        $this->assertEquals(new IssueResolution(), $this->fixture->getEntity('fixed'));
    }

    /**
     * @param string $key
     * @param array  $types
     * @dataProvider fillEntityDataProvider
     */
    public function testFillEntityData($key, $value)
    {
        $this->fixture->setTemplateManager($this->getTemplateManager());

        $issueResolution = new IssueResolution();

        $this->fixture->fillEntityData($key, $issueResolution);
        $this->assertEquals($value, $issueResolution->getName());
    }

    /**
     * @return array
     */
    public function fillEntityDataProvider()
    {
        return [
            'fixed'      => [
                'key'   => 'fixed',
                'value' => IssueResolution::RESOLUTION_FIXED
            ],
            'worksforme' => [
                'key'   => 'worksforme',
                'value' => IssueResolution::RESOLUTION_WORKSFORME
            ],
            'invalid'    => [
                'key'   => 'invalid',
                'value' => IssueResolution::RESOLUTION_INVALID
            ],
            'wontfix'    => [
                'key'   => 'wontfix',
                'value' => IssueResolution::RESOLUTION_WONTFIX
            ],
            'incomplete' => [
                'key'   => 'incomplete',
                'value' => IssueResolution::RESOLUTION_INCOMPLETE
            ],
            'duplicate'  => [
                'key'   => 'duplicate',
                'value' => IssueResolution::RESOLUTION_DUPLICATE
            ],

        ];
    }

    public function testGetData()
    {
        $this->fixture->setTemplateManager($this->getTemplateManager());

        $data = $this->fixture->getData();
        $this->assertCount(1, $data);

        /**
         * @var IssueResolution $resolution
         */
        $resolution = current($data);
        $this->assertInstanceOf('OroAcademy\Bundle\IssueBundle\Entity\IssueResolution', $resolution);
        $this->assertEquals('fixed', $resolution->getName());
    }

    /**
     * @return TemplateManager
     */
    protected function getTemplateManager()
    {
        $entityRegistry  = new TemplateEntityRegistry();
        $templateManager = new TemplateManager($entityRegistry);
        $templateManager->addEntityRepository($this->fixture);

        return $templateManager;
    }
}
