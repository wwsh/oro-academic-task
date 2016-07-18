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

use OroAcademy\Bundle\IssueBundle\Entity\IssueTypeRepository;

class IssueType extends AbstractType
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
                    'constraints'   => new NotNull(),
                    'required'      => true,
                    'query_builder' => function (IssueTypeRepository $repository) {
                        return $repository->getQueryBuilderFilteringSubtask();
                    },
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
                null
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
        return 'issue';
    }
}
