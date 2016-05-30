<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\EventListener;

use OroAcademy\Bundle\IssueBundle\EventListener\IssueNoteUpdatesListener;

class IssueNoteUpdatesListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IssueNoteUpdatesListener
     */
    private $item;

    protected function setUp()
    {
        $this->item = new IssueNoteUpdatesListener();
    }

    public function testPostPersist()
    {
        $issue = $this->getMock('OroAcademy\Bundle\IssueBundle\Entity\Issue');
        $note  = $this->getMock('Oro\Bundle\NoteBundle\Entity\Note');

        $note->expects($this->any())->method('getTarget')->will($this->returnValue($issue));

        $objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');

        $objectManager->expects($this->once())
            ->method('persist')->with($issue);

        $objectManager->expects($this->once())
            ->method('flush');

        $args = $this->getMockBuilder('Doctrine\Common\Persistence\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock();

        $args->expects($this->any())->method('getObjectManager')->will($this->returnValue($objectManager));
        $args->expects($this->any())->method('getObject')->will($this->returnValue($note));

        $this->item->postPersist($args);
    }

    /**
     * cloned from postPersist().
     * May be easily separated in the future.
     */
    public function testPostUpdate()
    {
        $issue = $this->getMock('OroAcademy\Bundle\IssueBundle\Entity\Issue');
        $note  = $this->getMock('Oro\Bundle\NoteBundle\Entity\Note');

        $note->expects($this->any())->method('getTarget')->will($this->returnValue($issue));

        $objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');

        $objectManager->expects($this->once())
            ->method('persist')->with($issue);

        $objectManager->expects($this->once())
            ->method('flush');

        $args = $this->getMockBuilder('Doctrine\Common\Persistence\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock();

        $args->expects($this->any())->method('getObjectManager')->will($this->returnValue($objectManager));
        $args->expects($this->any())->method('getObject')->will($this->returnValue($note));

        $this->item->postUpdate($args);
    }

    /**
     * cloned from postPersist().
     * May be easily separated in the future.
     */
    public function testPostRemove()
    {
        $issue = $this->getMock('OroAcademy\Bundle\IssueBundle\Entity\Issue');
        $note  = $this->getMock('Oro\Bundle\NoteBundle\Entity\Note');

        $note->expects($this->any())->method('getTarget')->will($this->returnValue($issue));

        $objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');

        $objectManager->expects($this->once())
            ->method('persist')->with($issue);

        $objectManager->expects($this->once())
            ->method('flush');

        $args = $this->getMockBuilder('Doctrine\Common\Persistence\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock();

        $args->expects($this->any())->method('getObjectManager')->will($this->returnValue($objectManager));
        $args->expects($this->any())->method('getObject')->will($this->returnValue($note));

        $this->item->postRemove($args);
    }
}
