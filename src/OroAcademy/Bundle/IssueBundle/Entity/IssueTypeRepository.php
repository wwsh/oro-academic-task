<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Entity;

use Doctrine\ORM\EntityRepository;

class IssueTypeRepository extends EntityRepository
{
    /**
     * Do not show the SUBTASK type in the dropdown.
     * Subtasks are gonna be added from the view screen [OOT-1461]
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderFilteringSubtask()
    {
        $qb = $this->createQueryBuilder('t');
        $qb = $qb->where($qb->expr()->neq('t.name', '?1'));
        $qb->setParameter(1, IssueType::TYPE_SUBTASK);
        return $qb;
    }
}
