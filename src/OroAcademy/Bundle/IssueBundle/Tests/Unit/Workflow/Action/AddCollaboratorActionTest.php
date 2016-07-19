<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\Workflow\Action;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Oro\Bundle\WorkflowBundle\Model\ContextAccessor;
use Oro\Bundle\NoteBundle\Entity\Note;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;

use OroAcademy\Bundle\IssueBundle\Entity\Issue;
use OroAcademy\Bundle\IssueBundle\Workflow\Action\AddCollaboratorAction;

class AddCollaboratorActionTest extends \PHPUnit_Framework_TestCase
{
    public function testExecution()
    {
        $dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
                           ->getMock();

        $accessor = $this->getMockBuilder(ContextAccessor::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $registry = $this->getMockBuilder(ManagerRegistry::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $context = $this->getMockBuilder(WorkflowItem::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $manager = $this->getMockBuilder(ObjectManager::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $action = new AddCollaboratorAction($accessor, $registry);
        $action->setDispatcher($dispatcher);

        $user = $this->getMockBuilder(User::class)
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

        $action->initialize(['assignee']);
        $action->execute($context);
    }

    public function testExecutionWithExplicitParameters()
    {
        $dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
                           ->getMock();

        $accessor = $this->getMockBuilder(ContextAccessor::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $registry = $this->getMockBuilder(ManagerRegistry::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $context = $this->getMockBuilder(WorkflowItem::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $manager = $this->getMockBuilder(ObjectManager::class)
                        ->disableOriginalConstructor()
                        ->getMock();


        $issue = $this->getMockBuilder(Issue::class)
                      ->getMock();

        $note = $this->getMockBuilder(Note::class)
                     ->getMock();

        $user = $this->getMockBuilder(User::class)
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

    /**
     * Only the Issue entities have to be affected.
     */
    public function testAffection()
    {
        $dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
                           ->getMock();

        $accessor = $this->getMockBuilder(ContextAccessor::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $registry = $this->getMockBuilder(ManagerRegistry::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $context = $this->getMockBuilder(WorkflowItem::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $foreignEntity = $this->getMockBuilder(User::class)
                              ->getMock();

        $note = $this->getMockBuilder(Note::class)
                     ->getMock();

        $explicitParameters = [
            'issue_object' => '$.data.issue',
            'note_object'  => '$.data'
        ];

        $foreignEntity->expects($this->never())
                      ->method('addCollaborator');

        $accessor->expects($this->at(0))
                 ->method('getValue')
                 ->with($context, '$.data.issue')
                 ->will($this->returnValue($foreignEntity));

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
