<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;

use OroAcademy\Bundle\IssueBundle\Entity\Issue;
use OroAcademy\Bundle\IssueBundle\Form\Handler\FormEntityRelationHelper;
use OroAcademy\Bundle\IssueBundle\Form\Handler\IssueHandler;
use OroAcademy\Bundle\IssueBundle\Helper\IssueFormBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

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
     * @var \PHPUnit_Framework_MockObject_MockObject|FormEntityRelationHelper
     */
    protected $helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|IssueFormBuilder
     */
    protected $builder;

    protected function setUp()
    {
        $this->form = $this->getMockBuilder('Symfony\Component\Form\Form')
                           ->disableOriginalConstructor()
                           ->getMock();

        $this->request = new Request();

        $this->manager = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->helper = $this->getMockBuilder('OroAcademy\Bundle\IssueBundle\Form\Handler\FormEntityRelationHelper')
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->builder = $this->getMockBuilder('OroAcademy\Bundle\IssueBundle\Helper\IssueFormBuilder')
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->entity  = new Issue();
        $this->handler = new IssueHandler(
            $this->helper,
            $this->request,
            $this->manager,
            $this->builder
        );

    }


    public function testProcessUnsupportedRequest()
    {
        $this->builder->method('createForm')
            ->with($this->entity)
            ->will($this->returnValue($this->form));

        $this->helper->expects($this->once())
                     ->method('getEntityData')
                     ->with($this->entity);

        $this->form->expects($this->never())
                   ->method('submit');


        $this->assertFalse($this->handler->process($this->entity));
    }

    /**
     * @dataProvider supportedMethods
     *
     * @param string $method
     */
    public function testProcessSupportedRequest($method)
    {
        $this->builder->method('createForm')
                      ->with($this->entity)
                      ->will($this->returnValue($this->form));

        $this->request->setMethod($method);

        $this->helper->expects($this->once())
                     ->method('getEntityData');

        $this->form->expects($this->once())
                   ->method('submit');

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
        $this->builder->method('createForm')
                      ->with($this->entity)
                      ->will($this->returnValue($this->form));

        $this->request->setMethod('POST');

        $entityProcessedData = [ ];

        $this->request->request->set('issue', [ ]);

        $this->helper->expects($this->once())
                     ->method('getEntityData')
                     ->will($this->returnValue($entityProcessedData));

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

        $this->assertTrue($this->handler->process($this->entity));
    }

    public function testProcessWithoutViewPermission()
    {
        $this->builder->method('createForm')
                      ->with($this->entity)
                      ->will($this->returnValue($this->form));

        $this->request->setMethod('POST');

        $entityProcessedData = [ ];

        $this->helper->expects($this->once())
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

        $this->assertTrue($this->handler->process($this->entity));
    }

    public function testSubtaskProcessing()
    {
        $this->builder->method('createForm')
                      ->with($this->entity)
                      ->will($this->returnValue($this->form));

        $this->request->setMethod('POST');

        $requestInputData = [
            'code' => 'ABC-123'
        ];

        $entityProcessedData = $requestInputData;

        $this->request->request->set('subtask', $requestInputData);

        $this->helper->expects($this->once())
                     ->method('getEntityData')
                     ->with($this->entity, $requestInputData)
                     ->will($this->returnValue($entityProcessedData));

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

        $this->assertTrue($this->handler->process($this->entity));
    }
}
