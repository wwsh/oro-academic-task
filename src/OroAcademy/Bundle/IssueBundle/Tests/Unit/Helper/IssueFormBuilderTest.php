<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\Helper;

use OroAcademy\Bundle\IssueBundle\Helper\IssueFormBuilder;
use Symfony\Component\HttpFoundation\Request;

class IssueFormBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateForm()
    {
        $request = new Request();

        $issue            = $this->getMock('OroAcademy\Bundle\IssueBundle\Entity\Issue');
        $request->request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $formFactory      = $this->getMockBuilder('Symfony\Component\Form\FormFactory')
                                 ->disableOriginalConstructor()
                                 ->getMock();
        $helper           = $this->getMock('OroAcademy\Bundle\IssueBundle\Helper\SubtaskFormHelper');

        $form = [ ];

        $helper->expects($this->at(0))->method('isSubtask')->with($issue)
               ->will($this->returnValue(false));
        $helper->expects($this->at(1))->method('isSubtask')->with($issue)
               ->will($this->returnValue(true));

        $formFactory->expects($this->at(0))->method('create')->with('issue', $issue)
                    ->will($this->returnValue($form));
        $formFactory->expects($this->at(1))->method('create')->with('subtask', $issue)
                    ->will($this->returnValue($form));

        $requestStack = $this->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')
                             ->disableOriginalConstructor()
                             ->getMock();

        $requestStack->method('getCurrentRequest')->will($this->returnValue($request));

        $builder = new IssueFormBuilder($helper, $formFactory, $requestStack);

        $this->assertEquals($form, $builder->createForm($issue));
        $this->assertEquals($form, $builder->createForm($issue));
    }
}