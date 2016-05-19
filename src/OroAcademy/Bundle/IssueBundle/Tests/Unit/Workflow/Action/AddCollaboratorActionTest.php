<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\Workflow\Action;

use OroAcademy\Bundle\IssueBundle\Entity\Issue;
use OroAcademy\Bundle\IssueBundle\Workflow\Action\AddCollaboratorAction;

class AddCollaboratorActionTest extends \PHPUnit_Framework_TestCase
{
    public function testExecution()
    {
        $dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')
                           ->getMock();

        $accessor = $this->getMockBuilder('Oro\Bundle\WorkflowBundle\Model\ContextAccessor')
                         ->disableOriginalConstructor()
                         ->getMock();

        $registry = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')
                         ->disableOriginalConstructor()
                         ->getMock();

        $context = $this->getMockBuilder('Oro\Bundle\WorkflowBundle\Entity\WorkflowItem')
                        ->disableOriginalConstructor()
                        ->getMock();

        $manager = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')
                        ->disableOriginalConstructor()
                        ->getMock();

        $action = new AddCollaboratorAction($accessor, $registry);
        $action->setDispatcher($dispatcher);

        $user = $this->getMockBuilder('Oro\Bundle\UserBundle\Entity\User')
                     ->disableOriginalConstructor()
                     ->getMock();

        $user->method('getFirstName')->will($this->returnValue('Thomas'));

        $issue = new Issue();
        $issue->setAssignee($user);

        $registry->expects($this->once())
                 ->method('getManager')
                 ->will($this->returnValue($manager));

        $accessor->expects($this->once())
                 ->method('getValue')
                 ->with($context, 'assignee')
                 ->will($this->returnValue($user));

        $context->expects($this->once())
                ->method('getEntity')
                ->will($this->returnValue($issue));

        $action->initialize([ 'assignee' ]);
        $action->execute($context);
    }

    public function testExecutionWithExplicitParameters()
    {
        $dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')
                           ->getMock();

        $accessor = $this->getMockBuilder('Oro\Bundle\WorkflowBundle\Model\ContextAccessor')
                         ->disableOriginalConstructor()
                         ->getMock();

        $registry = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')
                         ->disableOriginalConstructor()
                         ->getMock();

        $context = $this->getMockBuilder('Oro\Bundle\WorkflowBundle\Entity\WorkflowItem')
                        ->disableOriginalConstructor()
                        ->getMock();

        $manager = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')
                        ->disableOriginalConstructor()
                        ->getMock();


        $issue = $this->getMockBuilder('OroAcademy\Bundle\IssueBundle\Entity\Issue')
                      ->getMock();

        $note = $this->getMockBuilder('Oro\Bundle\NoteBundle\Entity\Note')
                     ->getMock();

        $user = $this->getMockBuilder('Oro\Bundle\UserBundle\Entity\User')
                     ->getMock();

        $note->expects($this->once())
             ->method('getOwner')
             ->will($this->returnValue($user));

        $issue->expects($this->once())
              ->method('addCollaborator')
              ->with($user);

        $explicitParameters = [
            'issue_object' => '$.data.issue',
            'note_object'  => '$.data'
        ];

        $registry->expects($this->once())
                 ->method('getManager')
                 ->will($this->returnValue($manager));

        $accessor->expects($this->at(0))
                 ->method('getValue')
                 ->with($context, '$.data.issue')
                 ->will($this->returnValue($issue));

        $accessor->expects($this->at(1))
                 ->method('getValue')
                 ->with($context, '$.data')
                 ->will($this->returnValue($note));

        $action = new AddCollaboratorAction($accessor, $registry);
        $action->setDispatcher($dispatcher);
        $action->initialize($explicitParameters);
        $action->execute($context);
    }
}