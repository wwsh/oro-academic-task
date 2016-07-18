<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\EntityBundle\Tools\EntityRoutingHelper;
use Oro\Bundle\FormBundle\Form\Handler\ApiFormHandler;
use Oro\Bundle\FormBundle\Utils\FormUtils;
use Oro\Bundle\UserBundle\Entity\User;
use OroAcademy\Bundle\IssueBundle\Entity\Issue;
use OroAcademy\Bundle\IssueBundle\Form\Helper\SubtaskFormHelper;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class IssueHandler extends ApiFormHandler
{
    /**
     * @var
     */
    private $options = [
        'csrf_protection'    => false,
        'allow_extra_fields' => true
    ];

    /**
     * @var SubtaskFormHelper
     */
    protected $subtaskFormHelper;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var false|User
     */
    protected $user;

    /**
     * @var EntityRoutingHelper
     */
    protected $routingHelper;

    /**
     * @param SubtaskFormHelper   $subtaskFormHelper
     * @param Request             $request
     * @param ObjectManager       $manager
     * @param FormFactory         $formFactory
     * @param TokenStorage        $tokenStorage
     * @param EntityRoutingHelper $entityRoutingHelper
     */
    public function __construct(
        SubtaskFormHelper $subtaskFormHelper,
        Request $request,
        ObjectManager $manager,
        FormFactory $formFactory,
        TokenStorage $tokenStorage,
        $entityRoutingHelper
    ) {
        parent::__construct($request, $manager);

        $this->subtaskFormHelper = $subtaskFormHelper;
        $this->formFactory       = $formFactory;
        $this->user              = $tokenStorage->getToken()->getUser();
        $this->routingHelper     = $entityRoutingHelper;
    }

    /**
     * @param Issue $issue
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    public function createForm(Issue $issue = null)
    {
        if (null === $issue) {
            $issue = $this->manager
                ->getRepository('OroAcademyIssueBundle:Issue')
                ->createIssue();
        }

        if ($this->subtaskFormHelper->isSubtask($issue, $this->request)
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

    /**
     * @param object $entity
     * @return bool
     */
    public function process($entity)
    {
        $this->enforceOrganizationAndReporter($entity);
        $this->form = $this->createForm($entity);
        $this->processEntityForWidget($entity);

        if (in_array($this->request->getMethod(), [ 'POST', 'PUT' ])) {
            $this->form->handleRequest($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess($entity);

                return true;
            }
        }

        return false;
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param Issue $issue
     */
    protected function enforceOrganizationAndReporter(Issue $issue)
    {
        if (null === $issue->getOrganization()) {
            $organization = $this->manager
                ->getRepository('OroOrganizationBundle:Organization')
                ->getFirst();
            $issue->setOrganization($organization);
        }
        if (null === $issue->getReporter()) {
            $issue->setReporter($this->user);
        }
    }

    /**
     * Autosetting the Assignee in the popup form.
     * Code copied from CRM. Needs refactoring. Todo
     *
     * @param Issue $entity
     */
    protected function processEntityForWidget($entity)
    {
        $action            = $this->routingHelper->getAction($this->request);
        $targetEntityClass = $this->routingHelper
            ->getEntityClassName($this->request);
        $targetEntityId    = $this->routingHelper->getEntityId($this->request);

        if ($targetEntityClass
            && !$entity->getId()
            && $this->request->getMethod() === 'GET'
            && $action === 'assign'
            && is_a($targetEntityClass, 'Oro\Bundle\UserBundle\Entity\User', true)
        ) {
            $entity->setAssignee(
                $this->routingHelper
                    ->getEntity($targetEntityClass, $targetEntityId)
            );
            FormUtils::replaceField($this->form, 'assignee', [ 'read_only' => true ]);
        }
    }
}
