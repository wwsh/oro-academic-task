<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Form\Handler\Api;

use Doctrine\Common\Persistence\ObjectManager;
use OroAcademy\Bundle\IssueBundle\Entity\Issue;
use OroAcademy\Bundle\IssueBundle\Form\Handler\IssueHandler as ParentIssueHandler;
use OroAcademy\Bundle\IssueBundle\Form\Helper\EntityAssociationHelper;
use OroAcademy\Bundle\IssueBundle\Form\Helper\SubtaskFormHelper;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class IssueHandler extends ParentIssueHandler
{
    /**
     * @var EntityAssociationHelper
     */
    protected $associationHelper;

    /**
     * IssueHandler constructor.
     *
     * @param EntityAssociationHelper $associationHelper
     * @param SubtaskFormHelper       $subtaskFormHelper
     * @param Request                 $request
     * @param ObjectManager           $manager
     * @param FormFactory             $formFactory
     */
    public function __construct(
        SubtaskFormHelper $subtaskFormHelper,
        Request $request,
        ObjectManager $manager,
        FormFactory $formFactory,
        TokenStorage $tokenStorage,
        EntityAssociationHelper $associationHelper
    ) {
        parent::__construct(
            $subtaskFormHelper,
            $request,
            $manager,
            $formFactory,
            $tokenStorage,
            null // we don't need this one here
        );

        $this->associationHelper = $associationHelper;
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

        $this->enforceOrganizationAndReporter($entity);
        $this->form = $this->createForm($entity);

        $requestData = $this->associationHelper
            ->getEntityData($entity, $requestData);

        if (in_array($this->request->getMethod(), [ 'POST', 'PUT' ])) {
            $requestData = $this->enforceOwnerField($entity, $requestData);
            $this->form->submit($requestData);

            if ($this->form->isValid()) {
                $this->onSuccess($entity);

                return true;
            }
        }

        return false;
    }

    /**
     * @param Issue       $entity
     * @param       $requestData
     */
    protected function enforceOwnerField($entity, $requestData)
    {
        if (empty($entity)
            || !($entity instanceof Issue)
            || null === $entity->getReporter()
        ) {
            return $requestData;
        }
        if (!isset($requestData['reporter'])) {
            $requestData['reporter'] = $entity->getReporter()->getId();
        }

        return $requestData;
    }
}
