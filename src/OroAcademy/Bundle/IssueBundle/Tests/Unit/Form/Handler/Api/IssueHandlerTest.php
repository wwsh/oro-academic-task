<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\Form\Handler\Api;

use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\UserBundle\Entity\User;
use OroAcademy\Bundle\IssueBundle\Entity\Issue;
use OroAcademy\Bundle\IssueBundle\Form\Handler\Api\IssueHandler;
use OroAcademy\Bundle\IssueBundle\Form\Helper\EntityAssociationHelper;
use OroAcademy\Bundle\IssueBundle\Form\Helper\SubtaskFormHelper;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class IssueHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FormInterface
     */
    protected $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ObjectManager
     */
    protected $manager;

    /**
     * @var IssueHandler
     */
    protected $handler;

    /**
     * @var Issue
     */
    protected $entity;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EntityAssociationHelper
     */
    protected $associationHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|SubtaskFormHelper
     */
    protected $subtaskHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FormFactory
     */
    protected $formFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|TokenStorage
     */
    protected $tokenStorage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|User
     */
    protected $user;


    protected function setUp()
    {
        $this->form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $this->user = $this->getMockBuilder('Oro\Bundle\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = new Request();

        $this->manager = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->associationHelper = $this->getMockBuilder(
            'OroAcademy\Bundle\IssueBundle\Form\Helper\EntityAssociationHelper'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->subtaskHelper = $this->getMockBuilder('OroAcademy\Bundle\IssueBundle\Form\Helper\SubtaskFormHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->formFactory = $this->getMockBuilder('Symfony\Component\Form\FormFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken')
            ->disableOriginalConstructor()
            ->getMock();

        $token->method('getUser')
            ->willReturn($this->user);

        $this->tokenStorage = $this->getMockBuilder(
            'Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->tokenStorage->method('getToken')
            ->willReturn($token);

        $this->entity  = new Issue();
        $this->handler = new IssueHandler(
            $this->subtaskHelper,
            $this->request,
            $this->manager,
            $this->formFactory,
            $this->tokenStorage,
            $this->associationHelper
        );

    }


    public function testProcessUnsupportedRequest()
    {

        $organizationRepository = $this->getMockBuilder(
            'Oro\Bundle\OrganizationBundle\Entity\Repository\OrganizationRepository'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $organization           = $this->getMock('Oro\Bundle\OrganizationBundle\Entity\Organization');

        $this->entity = $this->getMock('OroAcademy\Bundle\IssueBundle\Entity\Issue');

        $this->entity->expects($this->once())
            ->method('getOrganization')
            ->will($this->returnValue(null));

        $this->entity->expects($this->once())
            ->method('getReporter')
            ->will($this->returnValue(null));

        $organizationRepository->method('getFirst')
            ->willReturn($organization);

        $this->manager->expects($this->once())
            ->method('getRepository')
            ->with('OroOrganizationBundle:Organization')
            ->willReturn($organizationRepository);

        $this->entity->expects($this->once())
            ->method('setOrganization')
            ->with($organization);

        $this->entity->expects($this->once())
            ->method('setReporter')
            ->with($this->user);

        $this->associationHelper->expects($this->once())
            ->method('getEntityData')
            ->with($this->entity);

        $this->form->expects($this->never())
            ->method('submit');

        $this->formFactory->method('create')
            ->will($this->returnValue($this->form));

        $this->assertFalse($this->handler->process($this->entity));
    }

    /**
     * @dataProvider supportedMethods
     *
     * @param string $method
     */
    public function testProcessSupportedRequest($method)
    {
        $organizationRepository = $this->getMockBuilder(
            'Oro\Bundle\OrganizationBundle\Entity\Repository\OrganizationRepository'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $organization           = $this->getMock('Oro\Bundle\OrganizationBundle\Entity\Organization');

        $this->entity = $this->getMock('OroAcademy\Bundle\IssueBundle\Entity\Issue');

        $this->entity->expects($this->once())
            ->method('getOrganization')
            ->will($this->returnValue(null));

        $this->entity->expects($this->at(0))
            ->method('getReporter')
            ->will($this->returnValue($this->user));

        $this->entity->expects($this->at(1))
            ->method('getReporter')
            ->will($this->returnValue(null));

        $organizationRepository->method('getFirst')
            ->willReturn($organization);

        $this->manager->expects($this->once())
            ->method('getRepository')
            ->with('OroOrganizationBundle:Organization')
            ->willReturn($organizationRepository);

        $this->entity->expects($this->once())
            ->method('setOrganization')
            ->with($organization);

        $this->entity->expects($this->once())
            ->method('setReporter')
            ->with($this->user);

        $this->request->setMethod($method);

        $this->associationHelper->expects($this->once())
            ->method('getEntityData');

        $this->form->expects($this->once())
            ->method('submit');

        $this->formFactory->method('create')
            ->will($this->returnValue($this->form));

        $this->assertFalse($this->handler->process($this->entity));
    }

    public function supportedMethods()
    {
        return [
            [ 'POST' ],
            [ 'PUT' ]
        ];
    }

    public function testProcessValidData()
    {
        $this->request->setMethod('POST');

        $entityProcessedData = [ ];

        $this->request->request->set('issue', [ ]);

        $this->associationHelper->expects($this->once())
            ->method('getEntityData')
            ->will($this->returnValue($entityProcessedData));

        $organizationRepository = $this->getMockBuilder(
            'Oro\Bundle\OrganizationBundle\Entity\Repository\OrganizationRepository'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $organization           = $this->getMock('Oro\Bundle\OrganizationBundle\Entity\Organization');

        $this->entity = $this->getMock('OroAcademy\Bundle\IssueBundle\Entity\Issue');

        $this->entity->expects($this->once())
            ->method('getOrganization')
            ->will($this->returnValue(null));

        $this->entity->expects($this->at(0))
            ->method('getReporter')
            ->will($this->returnValue($this->user));

        $this->entity->expects($this->at(1))
            ->method('getReporter')
            ->will($this->returnValue(null));

        $organizationRepository->method('getFirst')
            ->willReturn($organization);

        $this->manager->expects($this->once())
            ->method('getRepository')
            ->with('OroOrganizationBundle:Organization')
            ->willReturn($organizationRepository);

        $this->entity->expects($this->once())
            ->method('setOrganization')
            ->with($organization);

        $this->entity->expects($this->once())
            ->method('setReporter')
            ->with($this->user);

        $this->form->expects($this->once())
            ->method('submit')
            ->with($entityProcessedData);

        $this->form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->manager->expects($this->once())
            ->method('persist')
            ->with($this->entity);

        $this->manager->expects($this->once())
            ->method('flush');

        $this->formFactory->method('create')
            ->will($this->returnValue($this->form));

        $this->assertTrue($this->handler->process($this->entity));
    }

    public function testProcessWithoutViewPermission()
    {
        $this->request->setMethod('POST');

        $entityProcessedData = [ ];

        $this->associationHelper->expects($this->once())
            ->method('getEntityData')
            ->will($this->returnValue($entityProcessedData));

        $this->form->expects($this->once())
            ->method('submit')
            ->with($entityProcessedData);

        $this->form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->form->expects($this->never())
            ->method('get');

        $this->formFactory->method('create')
            ->will($this->returnValue($this->form));

        $this->entity                  = $this->getMock('OroAcademy\Bundle\IssueBundle\Entity\Issue');
        $organizationRepository = $this->getMockBuilder(
            'Oro\Bundle\OrganizationBundle\Entity\Repository\OrganizationRepository'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $organization           = $this->getMock('Oro\Bundle\OrganizationBundle\Entity\Organization');

        $this->entity->expects($this->once())
            ->method('getOrganization')
            ->will($this->returnValue(null));

        $this->entity->expects($this->at(0))
            ->method('getReporter')
            ->will($this->returnValue($this->user));

        $this->entity->expects($this->at(1))
            ->method('getReporter')
            ->will($this->returnValue(null));

        $organizationRepository->method('getFirst')
            ->willReturn($organization);

        $this->manager->expects($this->once())
            ->method('getRepository')
            ->with('OroOrganizationBundle:Organization')
            ->willReturn($organizationRepository);

        $this->entity->expects($this->once())
            ->method('setOrganization')
            ->with($organization);

        $this->entity->expects($this->once())
            ->method('setReporter')
            ->with($this->user);

        $this->assertTrue($this->handler->process($this->entity));
    }

    public function testSubtaskProcessing()
    {
        $this->request->setMethod('POST');

        $requestInputData = [
            'code' => 'ABC-123'
        ];

        $entityProcessedData = $requestInputData;

        $this->entity = $this->getMock('OroAcademy\Bundle\IssueBundle\Entity\Issue');

        $this->request->request->set('subtask', $requestInputData);

        $this->associationHelper->expects($this->once())
            ->method('getEntityData')
            ->with($this->entity, $requestInputData)
            ->will($this->returnValue($entityProcessedData));

        $this->form->expects($this->once())
            ->method('submit')
            ->with($entityProcessedData);

        $this->form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $organizationRepository = $this->getMockBuilder(
            'Oro\Bundle\OrganizationBundle\Entity\Repository\OrganizationRepository'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $organization           = $this->getMock('Oro\Bundle\OrganizationBundle\Entity\Organization');

        $this->entity->expects($this->once())
            ->method('getOrganization')
            ->will($this->returnValue(null));

        $this->entity->expects($this->at(0))
            ->method('getReporter')
            ->will($this->returnValue($this->user));

        $this->entity->expects($this->at(1))
            ->method('getReporter')
            ->will($this->returnValue(null));

        $organizationRepository->method('getFirst')
            ->willReturn($organization);

        $this->manager->expects($this->once())
            ->method('getRepository')
            ->with('OroOrganizationBundle:Organization')
            ->willReturn($organizationRepository);

        $this->entity->expects($this->once())
            ->method('setOrganization')
            ->with($organization);

        $this->entity->expects($this->once())
            ->method('setReporter')
            ->with($this->user);

        $this->manager->expects($this->once())
            ->method('persist')
            ->with($this->entity);

        $this->manager->expects($this->once())
            ->method('flush');

        $this->formFactory->method('create')
            ->will($this->returnValue($this->form));

        $this->assertTrue($this->handler->process($this->entity));
    }

    public function testCreateForm()
    {
        $request = new Request();

        $issueRepository = $this->getMockBuilder('OroAcademy\Bundle\IssueBundle\Entity\IssueRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $issue            = $this->getMock('OroAcademy\Bundle\IssueBundle\Entity\Issue');
        $request->request = $this->getMock('Symfony\Component\HttpFoundation\Request');

        $form = [ ];

        $this->subtaskHelper->expects($this->at(0))->method('isSubtask')->with($issue)
            ->will($this->returnValue(false));
        $this->subtaskHelper->expects($this->at(1))->method('isSubtask')->with($issue)
            ->will($this->returnValue(true));
        $this->subtaskHelper->expects($this->at(2))->method('isSubtask')->with($issue)
            ->will($this->returnValue(false));

        $this->formFactory->expects($this->at(0))->method('create')->with('issue', $issue)
            ->will($this->returnValue($form));
        $this->formFactory->expects($this->at(1))->method('create')->with('subtask', $issue)
            ->will($this->returnValue($form));
        $this->formFactory->expects($this->at(2))->method('create')->with('issue', $issue)
            ->will($this->returnValue($form));

        $issueRepository->expects($this->once())
            ->method('createIssue')
            ->willReturn($issue);

        $this->manager->expects($this->at(0))
            ->method('getRepository')
            ->with('OroAcademyIssueBundle:Issue')
            ->willReturn($issueRepository);

        $this->assertEquals($form, $this->handler->createForm($issue));
        $this->assertEquals($form, $this->handler->createForm($issue));
        $this->assertEquals($form, $this->handler->createForm(null));
    }
}
