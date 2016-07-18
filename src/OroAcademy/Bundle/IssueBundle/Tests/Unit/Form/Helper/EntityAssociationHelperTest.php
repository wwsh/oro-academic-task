<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\Form\Helper;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

use OroAcademy\Bundle\IssueBundle\Entity\Issue;
use OroAcademy\Bundle\IssueBundle\Entity\IssuePriority;
use OroAcademy\Bundle\IssueBundle\Form\Helper\EntityAssociationHelper;


class EntityAssociationHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityAssociationHelper
     */
    private $item;

    /**
     * @var ObjectManager
     */
    private $manager;

    public function testConvertingRelationDataIntoEntityData()
    {
        $em = $this->getMockBuilder(ObjectManager::class)
                   ->disableOriginalConstructor()
                   ->getMock();

        $classMetadataIssue = new \stdClass();

        $classMetadataIssue->associationMappings = [
            'priority' => [
                'type'         => ClassMetadataInfo::MANY_TO_ONE,
                'targetEntity' => IssuePriority::class
            ]
        ];

        $em->expects($this->at(0))
           ->method('getClassMetadata')
           ->with(Issue::class)
           ->willReturn($classMetadataIssue);

        $classMetadataIssuePriority = new \stdClass();

        $classMetadataIssuePriority->fieldMappings = [
            'name' => [
                'type' => 'string'
            ]
        ];

        $em->expects($this->at(1))
           ->method('getClassMetadata')
           ->with(IssuePriority::class)
           ->willReturn($classMetadataIssuePriority);

        $issuePriorityRepo = $this->getMockBuilder(EntityRepository::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $issuePriorityRepo->expects($this->once())
                          ->method('findOneBy')
                          ->with(['name' => 'high'])
                          ->willReturn(new Issue());

        $em->expects($this->once())
           ->method('getRepository')
           ->with(IssuePriority::class)
           ->willReturn($issuePriorityRepo);

        $this->manager = $em;

        $this->item = new EntityAssociationHelper($em);

        $issue = new Issue();

        $data = [
            "code"        => 'ABC-123',
            "summary"     => "Test summary",
            "description" => "Test description",
            "type"        => "bug",
            "priority"    => "high",
            "reporter"    => "admin",
            "parent"      => ""
        ];

        $this->item->getEntityData($issue, $data);
    }
}
