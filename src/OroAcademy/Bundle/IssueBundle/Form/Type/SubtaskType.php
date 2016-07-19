<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class SubtaskType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'type',
                'entity',
                [
                    'class'     => 'OroAcademyIssueBundle:IssueType',
                    'read_only' => true
                ]
            )
            ->add(
                'parent',
                'entity',
                [
                    'class'     => 'OroAcademyIssueBundle:Issue',
                    'read_only' => true
                ]
            )
            ->add(
                'code',
                'text',
                [
                    'required' => false
                ]
            )
            ->add(
                'priority',
                'entity',
                [
                    'class'       => 'OroAcademyIssueBundle:IssuePriority',
                    'constraints' => new NotNull(),
                    'required'    => true
                ]
            )
            ->add(
                'summary',
                'text',
                [
                    'constraints' => new NotBlank(),
                    'required'    => true
                ]
            )
            ->add(
                'description',
                'oro_resizeable_rich_text',
                [
                    'constraints' => new NotBlank(),
                    'required'    => true,
                    'label'       => 'oroacademy.issue.description.label'
                ]
            )
            ->add(
                'assignee',
                'oro_user_organization_acl_select',
                [
                    'label'    => 'oroacademy.issue.assignee.label',
                    'required' => false
                ]
            )
            ->add(
                'reporter',
                'entity',
                [
                    'class' => 'OroUserBundle:User'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'OroAcademy\Bundle\IssueBundle\Entity\Issue',
            ]
        );
    }

    public function getName()
    {
        return 'subtask';
    }
}
