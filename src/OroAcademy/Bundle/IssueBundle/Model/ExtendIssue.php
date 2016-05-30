<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Model;

use Oro\Bundle\ActivityBundle\Model\ActivityInterface;
use Oro\Bundle\ActivityBundle\Model\ExtendActivity;

/**
 * Class ExtendIssue
 *
 * @package OroAcademy\Bundle\IssueBundle\Model
 */
class ExtendIssue implements ActivityInterface
{
    use ExtendActivity;

    /**
     * ExtendIssue constructor.
     */
    public function __construct()
    {
    }
}
