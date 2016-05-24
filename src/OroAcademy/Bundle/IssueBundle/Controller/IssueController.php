<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Controller;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
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
        return [
            'gridName'     => 'issue-grid',
            'entity_class' => $this->container->getParameter('oroacademy_issue.entity.class')
        ];
    }

    /**
     * @Route("/mine/", name="oroacademy_my_issue_grid")
     * @Template
     * @AclAncestor("view_issue")
     */
    public function mineIssuesAction()
    {
        $userId  = $this->getUser()->getId();
        
        return [
            'gridName'     => 'my-issue-grid',
            'entity_class' => $this->container
                ->getParameter('oroacademy_issue.entity.class'),
            'userId'       => $userId
        ];
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
     * @Route("/create/subtask/{parent}", name="oroacademy_create_subtask_issue", requirements={"parent"="\d+"})
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
    public function createSubtaskAction(Issue $parent, Request $request)
    {
        $issue = $this->get('doctrine')
                      ->getRepository('OroAcademyIssueBundle:Issue')
                      ->createSubtask($parent);

        $result = $this->updateAction($issue, $request);
        if (!is_array($result)) {
            return $result;
        }

        $result['parent'] = $parent->getId();
        return $result;
    }

    /**
     * @Route("/update/{id}", name="oroacademy_update_issue", requirements={"id"="\d+"})
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
        $form = $this->get('oroacademy_issue_form_builder')
                     ->createForm($issue);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleIssueUpdate($issue);

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
     * @Route("/view/{id}", name="oroacademy_view_issue", requirements={"id"="\d+"})
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

    /**
     * @param Issue $issue
     * @return array
     *
     * @Route("/widget/details/{id}", name="oroacademy_issue_details_widget", requirements={"id"="\d+"})
     * @Template
     * @AclAncestor("oroacademy_view_issue")
     */
    public function detailsAction(Issue $issue)
    {
        return [ 'entity' => $issue ];
    }

    /**
     * @param Issue $issue
     * @return array
     *
     * @Route("/widget/links/{id}", name="oroacademy_issue_links_widget", requirements={"id"="\d+"})
     * @Template
     * @AclAncestor("oroacademy_view_issue")
     */
    public function linksAction(Issue $issue)
    {
        return [ 'entity' => $issue ];
    }

    /**
     * Almost done. Enforce organization set.
     *
     * todo: Refactor this into an IssueFormHandler
     *
     * @param Issue $issue
     */
    protected function handleIssueUpdate(Issue $issue)
    {
        $entityManager = $this->getDoctrine()->getManager();

        if (null === $issue->getOrganization()) {
            $organization = $entityManager->getRepository('OroOrganizationBundle:Organization')
                                          ->getFirst();
            $issue->setOrganization($organization);
        }
        if (null === $issue->getReporter()) {
            $token = $this->get('security.token_storage')->getToken();

            $issue->setReporter($token->getUser());
        }
        $entityManager->persist($issue);
        $entityManager->flush();
    }
}