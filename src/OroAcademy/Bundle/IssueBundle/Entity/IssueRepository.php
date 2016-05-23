<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Entity;

/**
 * IssueRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class IssueRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param Issue $parent
     * @return Issue
     */
    public function createSubtask(Issue $parent)
    {
        $issue = new Issue();

        $issue->setParent($parent);

        $repo = $this->getEntityManager()
                     ->getRepository('OroAcademyIssueBundle:IssueType');

        $subtaskType = $repo->findOneBy([ 'name' => IssueType::TYPE_SUBTASK ]);
        $issue->setType($subtaskType);

        return $issue;
    }

    /**
     * @return array
     */
    public function getIssuesByStatus()
    {
        $queryBuilder = $this->getEntityManager()
                             ->createQueryBuilder();

        $queryBuilder->select('wfs.label as label', 'COUNT(issue.id) as number')
                     ->from('OroAcademyIssueBundle:Issue', 'issue')
                     ->leftJoin('OroWorkflowBundle:WorkflowStep', 'wfs', 'WITH', 'wfs = issue.workflowStep')
                     ->groupBy('wfs.label');

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
