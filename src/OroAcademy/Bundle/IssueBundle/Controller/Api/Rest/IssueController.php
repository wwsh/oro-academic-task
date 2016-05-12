<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Controller\Api\Rest;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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

    public function getForm()
    {
        return $this->get('form.issue');
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