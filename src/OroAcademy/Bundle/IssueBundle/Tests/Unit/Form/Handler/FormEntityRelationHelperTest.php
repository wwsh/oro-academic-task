<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use OroAcademy\Bundle\IssueBundle\Entity\Issue;
use OroAcademy\Bundle\IssueBundle\Entity\IssuePriority;
use OroAcademy\Bundle\IssueBundle\Entity\IssueType;
use OroAcademy\Bundle\IssueBundle\Form\Handler\FormEntityRelationHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FormEntityRelationHelperTest extends KernelTestCase
{
    /**
     * @var FormEntityRelationHelper
     */
    private $item;

    /**
     * @var ObjectManager
     */
    private $manager;

    protected function setUp()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();

        $em = $container->get('doctrine.orm.entity_manager');

        $this->manager = $em;

        $this->item = new FormEntityRelationHelper($em);
    }

    public function testConvertingRelationDataIntoEntityData()
    {
        $issue = new Issue();

        $data = [
            "code"        => 'ABC-123',
            "summary"     => "Test summary",
            "description" => "Test description",
            "type"        => "bug",
            "priority"    => "high",
            "reporter"    => "admin",
        ];

        $newData = $this->item->getEntityData($issue, $data);

        $this->assertNotNull($newData);

        $repo = $this->manager->getRepository('OroAcademyIssueBundle:IssueType');
        $issueType = $repo->findOneBy(['name'=>'bug']);
        $repo = $this->manager->getRepository('OroAcademyIssueBundle:IssuePriority');
        $issuePriority = $repo->findOneBy(['name'=>'high']);

        $this->assertEquals($issueType->getId(), $newData['type']);
        $this->assertEquals($issuePriority->getId(), $newData['priority']);
    }
}