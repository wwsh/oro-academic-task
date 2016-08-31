<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\FormBundle\Form\Type\OroResizeableRichTextType;
use Oro\Bundle\FormBundle\Form\Type\OroRichTextType;
use Oro\Bundle\FormBundle\Provider\HtmlTagProvider;
use Oro\Bundle\UserBundle\Form\Type\OrganizationUserAclSelectType;
use Oro\Component\Testing\Unit\FormIntegrationTestCase;

use OroAcademy\Bundle\IssueBundle\Entity\Issue;
use OroAcademy\Bundle\IssueBundle\Entity\IssueType as EntityIssueType;
use OroAcademy\Bundle\IssueBundle\Form\Type\IssueType;

class IssueTypeTest extends FormIntegrationTestCase
{
    /**
     * @var IssueType
     */
    protected $formType;

    /**
     * @var ConfigManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configManager;

    /**
     * @var HtmlTagProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $htmlTagProvider;

    public function setUp()
    {
        $this->formType = new IssueType();

        $this->configManager = $this->getMockBuilder(ConfigManager::class)
            ->disableOriginalConstructor()->getMock();

        $this->htmlTagProvider = $this->getMockBuilder(HtmlTagProvider::class)
            ->disableOriginalConstructor()->getMock();

        $this->htmlTagProvider->method('getAllowedElements')->willReturn([]);

        parent::setUp();
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
        $this->assertEquals('issue', $this->formType->getName());
    }

    public function testBuildForm()
    {
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $builder->expects($this->exactly(7))
            ->method('add')
            ->will($this->returnSelf());

        $builder->expects($this->at(0))
            ->method('add')
            ->with('type', 'entity');

        $builder->expects($this->at(1))
            ->method('add')
            ->with('code', 'text');

        $builder->expects($this->at(2))
            ->method('add')
            ->with('priority', 'entity');

        $builder->expects($this->at(3))
            ->method('add')
            ->with('summary', 'text');

        $builder->expects($this->at(4))
            ->method('add')
            ->with('description', 'oro_resizeable_rich_text');

        $builder->expects($this->at(5))
            ->method('add')
            ->with('assignee', 'oro_user_organization_acl_select');

        $builder->expects($this->at(6))
            ->method('add')
            ->with('reporter', 'entity');

        $this->formType->buildForm($builder, []);
    }

    /**
     * @param       $isValid
     * @param       $defaultData
     * @param       $submittedData
     * @param       $expectedData
     * @param array $options
     */
    public function testSubmit()
    {
        $defaultIssue = new Issue();
        $defaultIssue->setCode('ABC-101');
        $defaultIssue->setSummary('Some summary');
        $defaultIssue->setDescription('Some text');
        $defaultIssue->setCreatedAt(new \DateTime());

        // we're only testing one entity type field of the form
        // if you would also like to test priority, resolution etc
        // similar entity fields, u need to follow the concept
        // of the mocked resolver (see $mockEntityType)
        $issueType = new EntityIssueType(EntityIssueType::TYPE_BUG);
        $defaultIssue->setType($issueType);

        $submittedData = [
            'code'        => 'ZYX-101',
            'type'        => new EntityIssueType(EntityIssueType::TYPE_BUG),
            'description' => 'New text',
            'summary'     => 'New summary',
            'updatedAt'   => new \DateTime()
        ];

        $form = $this->factory->create($this->formType, $defaultIssue, []);
        /** @var Issue $formData */
        $formData = $form->getData();
        $this->assertEquals($defaultIssue->getCode(), $formData->getCode());
        $this->assertEquals($defaultIssue->getSummary(), $formData->getSummary());
        $this->assertEquals(
            $defaultIssue->getDescription(),
            $formData->getDescription()
        );
        $this->assertEquals(
            $defaultIssue->getType()->getLabel(),
            $formData->getType()->getLabel()
        );
        $form->submit($submittedData);

        $this->assertEquals(true, $form->isValid());

        /** @var Issue $formData */
        $formData = $form->getData();
        $this->assertEquals($submittedData['code'], $formData->getCode());
        $this->assertEquals($submittedData['summary'], $formData->getSummary());
        $this->assertEquals(
            $submittedData['description'],
            $formData->getDescription()
        );
        $this->assertEquals(
            $submittedData['type']->getLabel(),
            $formData->getType()->getLabel()
        );
    }

    /**
     * Mocking the entity type and Oro type fields.
     *
     * @return array
     */
    protected function getExtensions()
    {
        $mockEntityType = $this->getMockBuilder(EntityType::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockEntityType->expects($this->any())->method('getName')
            ->will($this->returnValue('entity'));

        $mockEntityType->expects($this->any())->method('setDefaultOptions')->will(
            $this->returnCallback(
                function (OptionsResolver $resolver) {
                    $resolver->setDefaults(
                        [
                            'constraints'   => null,
                            'class'         => null,
                            'query_builder' => null,
                            'required'      => null,
                            'data_class'    => EntityIssueType::class
                        ]
                    );
                }
            )
        );

        $mockResizeableRichTextType = $this
            ->getMockBuilder(OroResizeableRichTextType::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockResizeableRichTextType->expects($this->any())->method('getName')
            ->will($this->returnValue('oro_resizeable_rich_text'));

        $mockResizeableRichTextType->expects($this->any())
            ->method('setDefaultOptions')->will(
                $this->returnCallback(
                    function (OptionsResolver $resolver) {
                        $resolver->setDefaults(
                            [
                                'constraints' => null,
                                'label'       => null,
                                'required'    => null,
                            ]
                        );
                    }
                )
            );

        $mockRichTextType = $this->getMockBuilder(OroRichTextType::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockRichTextType->expects($this->any())->method('getName')
            ->will($this->returnValue('oro_rich_text'));

        $mockOrganizationUserAclSelectType = $this
            ->getMockBuilder(OrganizationUserAclSelectType::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockOrganizationUserAclSelectType->expects($this->any())->method('getName')
            ->will($this->returnValue('oro_user_organization_acl_select'));

        $mockOrganizationUserAclSelectType->expects($this->any())
            ->method('setDefaultOptions')->will(
                $this->returnCallback(
                    function (OptionsResolver $resolver) {
                        $resolver->setDefaults(
                            [
                                'label'    => null,
                                'required' => null,
                            ]
                        );
                    }
                )
            );

        return [
            new PreloadedExtension(
                [
                    $mockEntityType->getName()                    =>
                        $mockEntityType,
                    $mockResizeableRichTextType->getName()        =>
                        $mockResizeableRichTextType,
                    $mockRichTextType->getName()                  =>
                        $mockRichTextType,
                    $mockOrganizationUserAclSelectType->getName() =>
                        $mockOrganizationUserAclSelectType
                ],
                []
            )
        ];
    }
}
