<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\Helper;

use OroAcademy\Bundle\IssueBundle\Entity\IssueType;
use OroAcademy\Bundle\IssueBundle\Helper\SubtaskFormHelper;
use Symfony\Component\HttpFoundation\Request;

class SubtaskFormHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSubtask()
    {
        $helper = new SubtaskFormHelper();

        $request   = new Request();
        $issue     = $this->getMock('OroAcademy\Bundle\IssueBundle\Entity\Issue');
        $issueType = $this->getMock('OroAcademy\Bundle\IssueBundle\Entity\IssueType');

        $request->request = $this->getMock('Symfony\Component\HttpFoundation\Request');

        $issueType->expects($this->at(0))
                  ->method('getName')
                  ->will($this->returnValue(IssueType::TYPE_STORY));

        $issueType->expects($this->at(1))
                  ->method('getName')
                  ->will($this->returnValue(IssueType::TYPE_SUBTASK));

        $issue->method('getType')
              ->will($this->returnValue($issueType));

        $request->request->expects($this->at(0))
                         ->method('get')
                         ->with('subtask')
                         ->will($this->returnValue(null));

        $request->request->expects($this->at(1))
                         ->method('get')
                         ->with('subtask')
                         ->will($this->returnValue(null));

        $request->request->expects($this->at(2))
                         ->method('get')
                         ->with('subtask')
                         ->will($this->returnValue(1));

        $request->request->expects($this->at(3))
                         ->method('get')
                         ->with('subtask')
                         ->will($this->returnValue(1));

        $this->assertEquals(false, $helper->isSubtask($issue, $request));
        $this->assertEquals(true, $helper->isSubtask($issue, $request));
        $this->assertEquals(true, $helper->isSubtask($issue, $request));
        $this->assertEquals(true, $helper->isSubtask($issue, $request));
    }
}