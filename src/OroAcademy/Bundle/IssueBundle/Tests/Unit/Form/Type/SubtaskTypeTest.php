<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\Form\Type;

use Oro\Component\Testing\Unit\FormIntegrationTestCase;
use OroAcademy\Bundle\IssueBundle\Form\Type\SubtaskType;

class SubtaskTypeTest extends FormIntegrationTestCase
{
    /**
     * @var SubtaskType
     */
    protected $formType;

    public function setUp()
    {
        $this->formType = new SubtaskType();
    }

    public function testConfigureOptions()
    {
        /** @var OptionsResolver|\PHPUnit_Framework_MockObject_MockObject $resolver */
        $resolver = $this->getMock('Symfony\Component\OptionsResolver\OptionsResolver');

        $resolver->expects($this->once())
                 ->method('setDefaults')
                 ->with(['data_class' => 'OroAcademy\Bundle\IssueBundle\Entity\Issue']);

        $this->formType->configureOptions($resolver);
    }

    public function testGetName()
    {
        $this->assertEquals('subtask', $this->formType->getName());
    }

    public function testBuildForm()
    {
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
                        ->disableOriginalConstructor()
                        ->getMock();

        $builder->expects($this->exactly(8))
                ->method('add')
                ->will($this->returnSelf());

        $builder->expects($this->at(0))
                ->method('add')
                ->with('type', null);

        $builder->expects($this->at(1))
                ->method('add')
                ->with('parent', null);

        $builder->expects($this->at(2))
                ->method('add')
                ->with('code', null);

        $builder->expects($this->at(3))
                ->method('add')
                ->with('priority', null);

        $builder->expects($this->at(4))
                ->method('add')
                ->with('summary', null);

        $builder->expects($this->at(5))
                ->method('add')
                ->with('description', 'oro_resizeable_rich_text');

        $builder->expects($this->at(6))
                ->method('add')
                ->with('assignee', 'oro_user_organization_acl_select');

        $builder->expects($this->at(7))
                ->method('add')
                ->with('reporter', null);

        $this->formType->buildForm($builder, array());
    }
}
