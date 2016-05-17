<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Workflow\Action;

use Doctrine\Common\Persistence\ManagerRegistry;
use Oro\Bundle\NoteBundle\Entity\Note;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;
use Oro\Bundle\WorkflowBundle\Exception\InvalidParameterException;
use Oro\Bundle\WorkflowBundle\Model\Action\AbstractAction;
use Oro\Bundle\WorkflowBundle\Model\ContextAccessor;
use OroAcademy\Bundle\IssueBundle\Entity\Issue;
use Symfony\Component\PropertyAccess\PropertyPath;

/**
 * Class AddCollaboratorAction
 * @package OroAcademy\Bundle\IssueBundle\Workflow\Action
 */
class AddCollaboratorAction extends AbstractAction
{
    /**
     * @var array
     */
    private $collaboratorPropertyPath;

    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @var PropertyPath
     */
    private $issuePropertyPath;

    /**
     * @var PropertyPath
     */
    private $notePropertyPath;

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
        if (is_array($options) &&
            isset($options['issue_object']) &&
            isset($options['note_object'])
        ) {
            $this->issuePropertyPath = $options['issue_object'];
            $this->notePropertyPath  = $options['note_object'];

            return;
        }

        if (!isset($options[0])) {
            throw new InvalidParameterException('Collaborator object incorrectly specified.');
        }

        $this->collaboratorPropertyPath = $options[0];
    }

    /**
     * @param WorkflowItem $context
     */
    protected function executeAction($context)
    {
        if (!empty($this->issuePropertyPath) &&
            !empty($this->notePropertyPath)
        ) {
            /** @var Issue $issue */
            $issue = $this->contextAccessor->getValue($context, $this->issuePropertyPath);
            /** @var Note $note */
            $note = $this->contextAccessor->getValue($context, $this->notePropertyPath);

            $user = $note->getOwner();
            $issue->addCollaborator($user);

            return $this->persist($issue);
        }

        /* @var Issue $issue */
        $issue = $context->getEntity();

        $user = $this->contextAccessor->getValue($context, $this->collaboratorPropertyPath);

        $issue->addCollaborator($user);

        return $this->persist($issue);
    }

    /**
     * @param $issue
     */
    private function persist($issue)
    {
        $manager = $this->registry->getManager();

        $manager->persist($issue);
        $manager->flush($issue);
    }
}