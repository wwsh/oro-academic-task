<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\ImportExport\TemplateFixture;

use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateEntityRegistry;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateManager;
use OroAcademy\Bundle\IssueBundle\Entity\IssueType;
use OroAcademy\Bundle\IssueBundle\ImportExport\TemplateFixture\IssueTypeFixture;

class IssueTypeFixtureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IssueTypeFixture
     */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new IssueTypeFixture();
    }

    public function testGetEntityClass()
    {
        $this->assertEquals('OroAcademy\Bundle\IssueBundle\Entity\IssueType', $this->fixture->getEntityClass());
    }

    public function testCreateEntity()
    {
        $this->fixture->setTemplateManager($this->getTemplateManager());

        $this->assertEquals(new IssueType(), $this->fixture->getEntity('bug'));
    }

    /**
     * @param string $key
     * @param array  $types
     * @dataProvider fillEntityDataProvider
     */
    public function testFillEntityData($key, $type)
    {
        $this->fixture->setTemplateManager($this->getTemplateManager());

        $issueType = new IssueType();

        $this->fixture->fillEntityData($key, $issueType);
        $this->assertEquals($type, $issueType->getName());
    }

    /**
     * @return array
     */
    public function fillEntityDataProvider()
    {
        return [
            'bug'     => [
                'key'  => 'bug',
                'type' => IssueType::TYPE_BUG
            ],
            'task'    => [
                'key'  => 'task',
                'type' => IssueType::TYPE_TASK
            ],
            'story'   => [
                'key'  => 'story',
                'type' => IssueType::TYPE_STORY
            ],
            'subtask' => [
                'key'  => 'subtask',
                'type' => IssueType::TYPE_SUBTASK
            ],
        ];
    }

    public function testGetData()
    {
        $this->fixture->setTemplateManager($this->getTemplateManager());

        $data = $this->fixture->getData();
        $this->assertCount(1, $data);

        /** @var IssueType $type */
        $type = current($data);
        $this->assertInstanceOf('OroAcademy\Bundle\IssueBundle\Entity\IssueType', $type);
        $this->assertEquals('bug', $type->getName());
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