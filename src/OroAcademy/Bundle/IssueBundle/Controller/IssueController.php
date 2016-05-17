<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Controller;

use OroAcademy\Bundle\IssueBundle\Entity\Issue;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
     *     id="view_issue",
     *     type="entity",
     *     class="OroAcademy\Bundle\IssueBundle\Entity\Issue",
     *     permission="VIEW"
     * )
     */
    public function indexAction()
    {
        return [ 'gridName' => 'issue-grid' ];
    }

    /**
     * @Route("/create", name="oroacademy_create_issue")
     * @Template
     * @Acl(
     *     id="issue_create",
     *     type="entity",
     *     class="OroAcademy\Bundle\IssueBundle\Entity\Issue",
     *     permission="CREATE"
     * )
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request)
    {
        $issue = new Issue();
        return $this->updateAction($issue, $request);
    }

    /**
     * @Route("/update/{id}", name="oroacademy_update_issue")
     * @Template
     *
     * @Acl(
     *     id="update_issue",
     *     type="entity",
     *     class="OroAcademy\Bundle\IssueBundle\Entity\Issue",
     *     permission="EDIT"
     * )
     * @param Issue   $issue
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateAction(Issue $issue, Request $request)
    {
        $form = $this->get('form.factory')->create('issue', $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($issue);
            $entityManager->flush();

            return $this->get('oro_ui.router')->redirectAfterSave(
                [
                    'route'      => 'oroacademy_issue_update',
                    'parameters' => [ 'id' => $issue->getId() ],
                ],
                [ 'route' => 'oroacademy_issue_index' ],
                $issue
            );
        }

        return [
            'entity' => $issue,
            'form'   => $form->createView(),
        ];
    }

    /**
     * @Route("/view/{id}", name="oroacademy_view_issue")
     * @Template
     *
     * @Acl(
     *     id="view_issue",
     *     type="entity",
     *     class="OroAcademy\Bundle\IssueBundle\Entity\Issue",
     *     permission="VIEW"
     * )
     * @param Issue $issue
     * @return array
     */
    public function viewAction(Issue $issue)
    {
        return [ 'entity' => $issue ];
    }
}