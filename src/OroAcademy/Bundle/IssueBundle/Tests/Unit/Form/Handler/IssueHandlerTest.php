<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;

use OroAcademy\Bundle\IssueBundle\Entity\Issue;
use OroAcademy\Bundle\IssueBundle\Form\Handler\IssueHandler;
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

    protected function setUp()
    {
        $this->form = $this->getMockBuilder('Symfony\Component\Form\Form')
                           ->disableOriginalConstructor()
                           ->getMock();

        $this->request = new Request();

        $this->manager = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->entity  = new Issue();
        $this->handler = new IssueHandler($this->form, $this->request, $this->manager);
    }


    public function testProcessUnsupportedRequest()
    {
        $this->form->expects($this->once())
                   ->method('setData')
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
        $this->request->setMethod($method);

        $this->form->expects($this->once())
                   ->method('setData')
                   ->with($this->entity);

        $this->form->expects($this->once())
                   ->method('submit')
                   ->with($this->request);

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

        $this->form->expects($this->once())
                   ->method('setData')
                   ->with($this->entity);

        $this->form->expects($this->once())
                   ->method('submit')
                   ->with($this->request);

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

    public function testProcessWithoutContactViewPermission()
    {
        $this->request->setMethod('POST');

        $this->form->expects($this->once())
                   ->method('setData')
                   ->with($this->entity);

        $this->form->expects($this->once())
                   ->method('submit')
                   ->with($this->request);

        $this->form->expects($this->once())
                   ->method('isValid')
                   ->will($this->returnValue(true));

        $this->form->expects($this->never())
                   ->method('get');

        $this->assertTrue($this->handler->process($this->entity));
    }
}
