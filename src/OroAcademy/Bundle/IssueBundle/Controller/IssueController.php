<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class IssueController
 * @package OroAcademy\Bundle\IssueBundle\Controller
 */
class IssueController extends Controller
{
    /**
     * @Route("/", name="oroacademy_issue_index")
     * @Template
     * @Acl(
     *     id="issue_view",
     *     type="entity",
     *     class="OroAcademy\Bundle\IssueBundle\Entity\Issue",
     *     permission="VIEW"
     * )
     */
    public function indexAction()
    {
        return [ 'gridName' => 'issue-grid' ];
    }
}