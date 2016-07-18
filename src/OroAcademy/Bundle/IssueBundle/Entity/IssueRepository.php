<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Entity;

class IssueRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return Issue
     */
    public function createIssue()
    {
        return new Issue();
    }

    /**
     * @param Issue $parent
     * @return Issue
     */
    public function createSubtask(Issue $parent)
    {
        $issue = $this->createIssue();

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

        $result = $queryBuilder->getQuery()->getArrayResult();

        return $this->rekeyStatusArrayResult($result);
    }

    /**
     * @param $result
     * @return array
     */
    private function rekeyStatusArrayResult($result)
    {
        if (empty($result)) {
            return $result;
        }

        $keys = array_column($result, 'label');

        return array_combine(
            $keys,
            $result
        );
    }
}
