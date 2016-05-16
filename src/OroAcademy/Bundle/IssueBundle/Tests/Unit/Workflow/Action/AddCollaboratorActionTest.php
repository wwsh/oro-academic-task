<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\Workflow\Action;

use Oro\Bundle\UserBundle\Entity\User;
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

        $user = new User();
        $user->setFirstName('Thomas');

        $issue = new Issue();
        $issue->setAssignee($user);

        $registry->expects($this->once())
                 ->method('getManagerForClass')
                 ->with($issue)
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
}