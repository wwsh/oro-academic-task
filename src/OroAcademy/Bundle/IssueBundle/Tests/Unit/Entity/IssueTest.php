<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\Entity;

use OroAcademy\Bundle\IssueBundle\Entity\Issue;

class IssueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Issue
     */
    protected $issue;

    protected function setUp()
    {
        $this->issue = new Issue();
    }

    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        $method = 'set' . ucfirst($property);
        $result = $this->issue->$method($value);

        $this->assertInstanceOf(get_class($this->issue), $result);
        $this->assertEquals($value, $this->issue->{'get' . $property}());
    }

    public function settersAndGettersDataProvider()
    {
        $type = $this->getMockBuilder('OroAcademy\Bundle\IssueBundle\Entity\IssueType')
                     ->disableOriginalConstructor()
                     ->getMock();

        $resolution = $this->getMockBuilder('OroAcademy\Bundle\IssueBundle\Entity\IssueResolution')
                           ->disableOriginalConstructor()
                           ->getMock();

        $status = $this->getMockBuilder('OroAcademy\Bundle\IssueBundle\Entity\IssueStatus')
                       ->disableOriginalConstructor()
                       ->getMock();

        $tags = [
            $this->getMockBuilder('Oro\Bundle\TagBundle\Entity\Tag')
                 ->disableOriginalConstructor()
                 ->getMock()
        ];

        $priority = $this->getMockBuilder('OroAcademy\Bundle\IssueBundle\Entity\IssuePriority')
                         ->disableOriginalConstructor()
                         ->getMock();

        return [
            [ 'summary', 'Example Summary' ],
            [ 'code', 'ZYX-8045' ],
            [ 'description', 'Example Description' ],
            [ 'type', $type ],
            [ 'resolution', $resolution ],
            [ 'status', $status ],
            [ 'reporter', $this->getMock('Oro\Bundle\UserBundle\Entity\User') ],
            [ 'assignee', $this->getMock('Oro\Bundle\UserBundle\Entity\User') ],
            [ 'tags', $tags ],
            [ 'priority', $priority ],
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

    public function testAddingTags()
    {
        $oneTag = $this->getMockBuilder('Oro\Bundle\TagBundle\Entity\Tag')
                       ->disableOriginalConstructor()
                       ->getMock();

        $anotherTag = $this->getMockBuilder('Oro\Bundle\TagBundle\Entity\Tag')
                           ->disableOriginalConstructor()
                           ->getMock();

        $this->issue->addTag($oneTag);
        $this->issue->addTag($anotherTag);

        $this->assertCount(2, $this->issue->getTags());
        $this->assertContains($oneTag, $this->issue->getTags());
        $this->assertContains($anotherTag, $this->issue->getTags());
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

}
