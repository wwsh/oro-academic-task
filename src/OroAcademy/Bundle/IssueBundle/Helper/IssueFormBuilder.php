<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Helper;

use OroAcademy\Bundle\IssueBundle\Entity\Issue;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class IssueFormBuilder
{
    /**
     * @var SubtaskFormHelper
     */
    private $helper;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var
     */
    private $options = [
        'csrf_protection'    => false,
        'allow_extra_fields' => true,
        'validation_groups'  => false
    ];

    /**
     * IssueFormBuilder constructor.
     * @param SubtaskFormHelper $helper
     * @param FormFactory       $formFactory
     * @param RequestStack      $requestStack
     */
    public function __construct(
        SubtaskFormHelper $helper,
        FormFactory $formFactory,
        RequestStack $requestStack
    ) {
        $this->helper      = $helper;
        $this->formFactory = $formFactory;
        $this->request     = $requestStack->getCurrentRequest();
    }

    /**
     * @param Issue $issue
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    public function createForm(Issue $issue = null)
    {
        if (null === $issue) {
            $issue = new Issue(); // still testable - simply pass a parameter
        }

        if ($this->helper->isSubtask($issue, $this->request)
        ) {
            $form = $this->formFactory
                ->create(
                    'subtask',
                    $issue,
                    $this->options
                );
        } else {
            $form = $this->formFactory
                ->create(
                    'issue',
                    $issue,
                    $this->options
                );
        }

        return $form;
    }
}