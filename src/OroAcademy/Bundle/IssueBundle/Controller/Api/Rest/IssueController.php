<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Controller\Api\Rest;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @RouteResource("issue")
 * @NamePrefix("oroacademy_api_")
 */
class IssueController extends RestController
{
    /**
     * REST DELETE issue
     *
     * @ApiDoc(
     *      description="Delete issue",
     *      resource=true
     * )
     * @Acl(
     *      id="api_delete_issue",
     *      type="entity",
     *      class="OroAcademy\Bundle\IssueBundle\Entity\Issue",
     *      permission="DELETE"
     * )
     */
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    /**
     * REST GET issue
     *
     * @param string $id
     *
     * @ApiDoc(
     *      description="Get issue",
     *      resource=true
     * )
     *
     * @Acl(
     *      id="api_get_issue",
     *      type="entity",
     *      class="OroAcademy\Bundle\IssueBundle\Entity\Issue",
     *      permission="VIEW"
     * )
     * @return Response
     */
    public function getAction($id)
    {
        return $this->handleGetRequest($id);
    }

    /**
     * REST GET list
     *
     * @QueryParam(
     *      name="page",
     *      requirements="\d+",
     *      nullable=true,
     *      description="Page number, starting from 1. Defaults to 1."
     * )
     * @QueryParam(
     *      name="limit",
     *      requirements="\d+",
     *      nullable=true,
     *      description="Number of items per page. defaults to 10."
     * )
     * @ApiDoc(
     *      description="Get all issue items",
     *      resource=true
     * )
     * @Acl(
     *      id="api_get_issues",
     *      type="entity",
     *      class="OroAcademy\Bundle\IssueBundle\Entity\Issue",
     *      permission="VIEW"
     * )
     * @param Request $request
     * @return Response
     */
    public function cgetAction(Request $request)
    {
        $page  = (int)$request->request->get('page', 1);
        $limit = (int)$request->request->get('limit', self::ITEMS_PER_PAGE);

        return $this->handleGetListRequest($page, $limit);
    }

    /**
     * REST PUT issue
     *
     * @param int $id Issue id
     *
     * @ApiDoc(
     *      description="Update issue",
     *      resource=true
     * )
     *
     * @Acl(
     *      id="api_put_issue",
     *      type="entity",
     *      class="OroAcademy\Bundle\IssueBundle\Entity\Issue",
     *      permission="EDIT"
     * )
     *
     * @return Response
     */
    public function putAction($id)
    {
        return $this->handleUpdateRequest($id);
    }

    /**
     * Create new issue
     *
     * @ApiDoc(
     *      description="Create new issue",
     *      resource=true
     * )
     *
     * @Acl(
     *      id="api_post_issue",
     *      type="entity",
     *      class="OroAcademy\Bundle\IssueBundle\Entity\Issue",
     *      permission="CREATE"
     * )
     */
    public function postAction()
    {
        return $this->handleCreateRequest();
    }

    /**
     * Validation depends on the task type, therefore form switch
     * is required.
     *
     * @return \OroAcademy\Bundle\IssueBundle\Form\Type\IssueType
     */
    public function getForm()
    {
        return $this->get('oroacademy_issue_form_builder')
                    ->createForm();
    }

    public function getFormHandler()
    {
        return $this->get('oroacademy_issue_handler.api');
    }

    public function getManager()
    {
        return $this->get('oroacademy_issue_manager.api');
    }
}