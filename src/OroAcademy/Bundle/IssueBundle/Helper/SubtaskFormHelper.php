<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Helper;

use OroAcademy\Bundle\IssueBundle\Entity\Issue;
use OroAcademy\Bundle\IssueBundle\Entity\IssueType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SubtaskFormHelper
 * @package OroAcademy\Bundle\IssueBundle\Helper
 */
class SubtaskFormHelper
{
    /**
     * @param Issue   $issue
     * @param Request $request
     * @return bool
     */
    public function isSubtask(Issue $issue, Request $request)
    {
        $subtask = $request->request->get('subtask');

        $type = $issue->getType();

        if (!empty($subtask)) {
            return true;
        }

        if (null === $type) {
            return false;
        }

        return $type->getName() === IssueType::TYPE_SUBTASK;
    }
}