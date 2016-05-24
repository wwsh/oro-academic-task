<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\Entity;

use OroAcademy\Bundle\IssueBundle\Entity\Issue;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class IssueTest extends KernelTestCase
{
    /**
     * @var Issue
     */
    protected $issue;

    public function setUp()
    {
        // even though we are not using any kernel functionality here
        // this is necessary in order for the sequential tests to run
        self::bootKernel();

        $this->issue = new Issue();
    }

    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        if (is_string($value) && strpos($value, '\\') !== false) {
            $value = $this->getMockBuilder($value)
                          ->disableOriginalConstructor()
                          ->getMock();
        }
        $method = 'set' . ucfirst($property);
        $result = $this->issue->$method($value);

        $this->assertInstanceOf(get_class($this->issue), $result);
        $this->assertEquals($value, $this->issue->{'get' . $property}());
    }

    public function settersAndGettersDataProvider()
    {
        $type = 'OroAcademy\Bundle\IssueBundle\Entity\IssueType';

        $resolution = 'OroAcademy\Bundle\IssueBundle\Entity\IssueResolution';

        $priority = 'OroAcademy\Bundle\IssueBundle\Entity\IssuePriority';

        $organization = 'Oro\Bundle\OrganizationBundle\Entity\Organization';

        return [
            [ 'summary', 'Example Summary' ],
            [ 'code', 'ZYX-8045' ],
            [ 'description', 'Example Description' ],
            [ 'type', $type ],
            [ 'resolution', $resolution ],
            [ 'reporter', 'Oro\Bundle\UserBundle\Entity\User' ],
            [ 'assignee', 'Oro\Bundle\UserBundle\Entity\User' ],
            [ 'priority', $priority ],
            [ 'organization', $organization ],
            [ 'createdAt', new \DateTime() ],
            [ 'updatedAt', new \DateTime() ],
        ];
    }

    public function testParent()
    {
        $parentIssue = new Issue();
        $this->issue->setParent($parentIssue);

        $this->assertEquals($parentIssue, $this->issue->getParent());
    }

    public function testChildren()
    {
        $firstIssue  = new Issue();
        $secondIssue = new Issue();
        $thirdIssue  = new Issue();

        $this->issue->addChild($firstIssue);
        $this->issue->addChild($secondIssue);
        $this->issue->addChild($thirdIssue);

        $this->assertCount(3, $this->issue->getChildren());
        $this->assertContains($firstIssue, $this->issue->getChildren());
        $this->assertContains($secondIssue, $this->issue->getChildren());
        $this->assertContains($thirdIssue, $this->issue->getChildren());
    }

    public function testAddingCollaborators()
    {
        $firstCollab  = $this->getMock('Oro\Bundle\UserBundle\Entity\User');
        $secondCollab = $this->getMock('Oro\Bundle\UserBundle\Entity\User');

        $this->issue->addCollaborator($firstCollab);
        $this->issue->addCollaborator($secondCollab);

        $this->assertCount(2, $this->issue->getCollaborators());
        $this->assertContains($firstCollab, $this->issue->getCollaborators());
        $this->assertContains($secondCollab, $this->issue->getCollaborators());
    }

    public function testCollaboratorsMustBeUnique()
    {
        $firstCollab  = $this->getMock('Oro\Bundle\UserBundle\Entity\User');
        $secondCollab = $firstCollab;

        $this->issue->addCollaborator($firstCollab);
        $this->issue->addCollaborator($secondCollab);

        $this->assertCount(1, $this->issue->getCollaborators());
    }

    public function testAddingRelatedIssues()
    {
        $firstIssue  = new Issue();
        $secondIssue = new Issue();
        $thirdIssue  = new Issue();

        $this->issue->addRelatedIssue($firstIssue);
        $this->issue->addRelatedIssue($secondIssue);
        $this->issue->addRelatedIssue($thirdIssue);

        $this->assertCount(3, $this->issue->getRelatedIssues());
        $this->assertContains($firstIssue, $this->issue->getRelatedIssues());
        $this->assertContains($secondIssue, $this->issue->getRelatedIssues());
        $this->assertContains($thirdIssue, $this->issue->getRelatedIssues());
    }

    /**
     * @dataProvider printDataProvider
     */
    public function testPrintAbility(Issue $issue, $expectedPrint)
    {
        $this->assertEquals($expectedPrint, (string)$issue);
    }

    public function printDataProvider()
    {
        return [
            [ new Issue('ABC-123', 'Composer problem'), '[ABC-123] Composer problem' ],
            [ new Issue('XYZ-456', 'Troubleshooting PhpUnit'), '[XYZ-456] Troubleshooting PhpUnit' ],
        ];
    }

    public function testGetSimpleCollaboratorArray()
    {
        $issue = new Issue();

        $this->assertEquals([ ], $issue->getSimpleCollaboratorArray());

        $userData = [
            [ 'Eddie', 'Smith' ],
            [ 'John', 'Carter' ]
        ];

        foreach ($userData as $oneUserData) {
            $user = $this->getMockBuilder('Oro\Bundle\UserBundle\Entity\User')
                         ->disableOriginalConstructor()
                         ->getMock();

            $user->expects($this->once())
                 ->method('getFirstName')
                 ->will($this->returnValue($oneUserData[0]));
            $user->expects($this->once())
                 ->method('getLastName')
                 ->will($this->returnValue($oneUserData[1]));

            $issue->addCollaborator($user);
        }

        $result = $issue->getSimpleCollaboratorArray();

        $this->assertCount(2, $result);
        $this->assertEquals('Eddie Smith', $result[0]);
        $this->assertEquals('John Carter', $result[1]);
    }
}
