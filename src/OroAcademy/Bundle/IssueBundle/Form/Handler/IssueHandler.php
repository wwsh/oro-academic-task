<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\FormBundle\Form\Handler\ApiFormHandler;
use Oro\Bundle\UserBundle\Entity\User;
use OroAcademy\Bundle\IssueBundle\Entity\Issue;
use OroAcademy\Bundle\IssueBundle\Form\Helper\EntityAssociationHelper;
use OroAcademy\Bundle\IssueBundle\Form\Helper\SubtaskFormHelper;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class IssueHandler.
 * Borrowing as much code as possible.
 * @package OroAcademy\Bundle\IssueBundle\Form\Handler
 */
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
     * @var EntityAssociationHelper
     */
    protected $associationHelper;

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
     * IssueHandler constructor.
     * @param EntityAssociationHelper $associationHelper
     * @param SubtaskFormHelper       $subtaskFormHelper
     * @param Request                 $request
     * @param ObjectManager           $manager
     * @param FormFactory             $formFactory
     */
    public function __construct(
        EntityAssociationHelper $associationHelper,
        SubtaskFormHelper $subtaskFormHelper,
        Request $request,
        ObjectManager $manager,
        FormFactory $formFactory,
        TokenStorage $tokenStorage
    ) {
        parent::__construct($request, $manager);

        $this->associationHelper = $associationHelper;
        $this->subtaskFormHelper = $subtaskFormHelper;
        $this->formFactory       = $formFactory;
        $this->user              = $tokenStorage->getToken()->getUser();
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
        $requestData = $this->request->request->get('issue');

        // the Subtask guess...
        if (empty($requestData)) {
            $requestData = $this->request->request->get('subtask');
        }

        $this->form = $this->createForm($entity);

        $requestData = $this->associationHelper
            ->getEntityData($entity, $requestData);

        if (in_array($this->request->getMethod(), [ 'POST', 'PUT' ])) {
            $this->form->submit($requestData);

            if ($this->form->isValid()) {
                $this->enforceOrganizationAndReporter($entity);
                $this->onSuccess($entity);

                return true;
            }
        }

        return false;
    }

    /**
     * @param Issue $issue
     */
    public function save(Issue $issue)
    {
        $this->enforceOrganizationAndReporter($issue);
        $this->manager->persist($issue);
        $this->manager->flush();
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
}
