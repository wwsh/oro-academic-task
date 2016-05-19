<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
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
                null,
                [
                    'read_only' => true
                ]
            )
            ->add(
                'parent',
                null,
                [
                    'read_only' => true
                ]
            )
            ->add(
                'code',
                null,
                [
                    'required' => false
                ]
            )
            ->add(
                'priority',
                null,
                [
                    'constraints' => new NotNull(),
                    'required'    => true
                ]
            )
            ->add(
                'summary',
                null,
                [
                    'constraints' => new NotBlank(),
                    'required'    => true
                ]
            )
            ->add(
                'description',
                null,
                [
                    'constraints' => new NotBlank(),
                    'required'    => true
                ]
            )
            ->add('tags')
            ->add(
                'reporter',
                'oro_user_organization_acl_select',
                [
                    'required'    => true,
                    'label'       => 'Reporter',
                    'constraints' => [
                        new NotNull()
                    ]
                ]
            )
            ->add(
                'assignee',
                'oro_user_organization_acl_select',
                [
                    'label'    => 'Assignee',
                    'required' => true
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