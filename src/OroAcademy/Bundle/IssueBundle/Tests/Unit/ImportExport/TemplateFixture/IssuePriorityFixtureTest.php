<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\ImportExport\TemplateFixture;

use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateEntityRegistry;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateManager;
use OroAcademy\Bundle\IssueBundle\Entity\IssuePriority;
use OroAcademy\Bundle\IssueBundle\ImportExport\TemplateFixture\IssuePriorityFixture;

class IssuePriorityFixtureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IssuePriorityFixture
     */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new IssuePriorityFixture();
    }

    public function testGetEntityClass()
    {
        $this->assertEquals('OroAcademy\Bundle\IssueBundle\Entity\IssuePriority', $this->fixture->getEntityClass());
    }

    public function testCreateEntity()
    {
        $this->fixture->setTemplateManager($this->getTemplateManager());

        $this->assertEquals(new IssuePriority(), $this->fixture->getEntity('normal'));
    }

    /**
     * @param string $key
     * @param array  $types
     * @dataProvider fillEntityDataProvider
     */
    public function testFillEntityData($key, $value)
    {
        $this->fixture->setTemplateManager($this->getTemplateManager());

        $issuePriority = new IssuePriority();

        $this->fixture->fillEntityData($key, $issuePriority);
        $this->assertEquals($value, $issuePriority->getName());
    }

    /**
     * @return array
     */
    public function fillEntityDataProvider()
    {
        return [
            'normal' => [
                'key'   => 'normal',
                'value' => IssuePriority::PRIORITY_NORMAL
            ],
            'low'    => [
                'key'   => 'low',
                'value' => IssuePriority::PRIORITY_LOW
            ],
            'high'   => [
                'key'   => 'high',
                'value' => IssuePriority::PRIORITY_HIGH
            ],
        ];
    }

    public function testGetData()
    {
        $this->fixture->setTemplateManager($this->getTemplateManager());

        $data = $this->fixture->getData();
        $this->assertCount(1, $data);

        /** @var IssuePriority $priority */
        $priority = current($data);
        $this->assertInstanceOf('OroAcademy\Bundle\IssueBundle\Entity\IssuePriority', $priority);
        $this->assertEquals('normal', $priority->getName());
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