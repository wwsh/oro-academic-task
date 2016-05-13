<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\FormBundle\Form\Handler\ApiFormHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class IssueHandler.
 * Borrowing as much code as possible.
 * @package OroAcademy\Bundle\IssueBundle\Form\Handler
 */
class IssueHandler extends ApiFormHandler
{
    /**
     * @var FormEntityRelationHelper
     */
    protected $helper;

    public function __construct(
        FormEntityRelationHelper $helper,
        Request $request,
        ObjectManager $manager
    ) {
        parent::__construct($request, $manager);

        $this->helper = $helper;
    }

    /**
     * @param object $entity
     * @return bool
     */
    public function process($entity)
    {
        $requestData = $this->request->request->get('issue');
        $requestData = $this->helper->getEntityData($entity, $requestData);

        $this->form->setData($entity);

        if (in_array($this->request->getMethod(), [ 'POST', 'PUT' ])) {
            $this->form->submit($requestData);

            if ($this->form->isValid()) {
                $this->onSuccess($entity);

                return true;
            }
        }

        return false;
    }


}
