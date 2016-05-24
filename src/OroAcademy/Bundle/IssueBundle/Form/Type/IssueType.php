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
use OroAcademy\Bundle\IssueBundle\Entity\IssueType as EntityIssueType;

/**
 * Class IssueType
 * @package OroAcademy\Bundle\IssueBundle\Form\Type
 */
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
                    'query_builder' => function (EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('t');
                        $qb = $qb->where($qb->expr()->neq('t.name', '?1'));
                        // do not show the SUBTASK type in the dropdown
                        // Subtasks are gonna be added from the view screen [OOT-1461]
                        $qb->setParameter(1, EntityIssueType::TYPE_SUBTASK);
                        return $qb;
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
                    'required'    => true
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
        return 'issue';
    }
}