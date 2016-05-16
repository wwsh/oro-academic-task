<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Workflow\Action;

use Doctrine\Common\Persistence\ManagerRegistry;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;
use Oro\Bundle\WorkflowBundle\Exception\InvalidParameterException;
use Oro\Bundle\WorkflowBundle\Model\Action\AbstractAction;
use Oro\Bundle\WorkflowBundle\Model\ContextAccessor;
use OroAcademy\Bundle\IssueBundle\Entity\Issue;

/**
 * Class AddCollaboratorAction
 * @package OroAcademy\Bundle\IssueBundle\Workflow\Action
 */
class AddCollaboratorAction extends AbstractAction
{
    /**
     * @var array
     */
    protected $collaborator;

    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @param ContextAccessor $contextAccessor
     * @param ManagerRegistry $registry
     */
    public function __construct(ContextAccessor $contextAccessor, ManagerRegistry $registry)
    {
        parent::__construct($contextAccessor);

        $this->registry = $registry;
    }


    /**
     * @param array $options
     * @return \Oro\Bundle\WorkflowBundle\Model\Action\ActionInterface|void
     * @throws InvalidParameterException
     */
    public function initialize(array $options)
    {
        if (!isset($options[0])) {
            throw new InvalidParameterException('Collaborator name incorrectly specified.');
        }

        $this->collaborator = $options[0];
    }

    /**
     * @param WorkflowItem $context
     */
    protected function executeAction($context)
    {
        /* @var Issue $issue */
        $issue = $context->getEntity();

        $user = $this->contextAccessor->getValue($context, $this->collaborator);

        $issue->addCollaborator($user);

        $manager = $this->registry->getManagerForClass($issue);

        $manager->persist($issue);
        $manager->flush($issue);
    }
}