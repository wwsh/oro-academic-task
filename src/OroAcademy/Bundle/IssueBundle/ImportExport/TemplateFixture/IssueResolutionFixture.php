<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\ImportExport\TemplateFixture;

use Oro\Bundle\ImportExportBundle\TemplateFixture\AbstractTemplateRepository;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateFixtureInterface;
use OroAcademy\Bundle\IssueBundle\Entity\IssueResolution;

class IssueResolutionFixture extends AbstractTemplateRepository implements TemplateFixtureInterface
{
    /**
     * @param string $key
     * @return IssueResolution
     */
    protected function createEntity($key)
    {
        return new IssueResolution();
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return 'OroAcademy\Bundle\IssueBundle\Entity\IssueResolution';
    }

    /**
     * @return \Iterator
     */
    public function getData()
    {
        return $this->getEntityData('fixed');
    }

    /**
     * @param string $key
     * @param object $entity
     */
    public function fillEntityData($key, $entity)
    {
        switch ($key) {
            case 'duplicate':
                $entity->setName(IssueResolution::RESOLUTION_DUPLICATE);
                return;
            case 'wontfix':
                $entity->setName(IssueResolution::RESOLUTION_WONTFIX);
                return;
            case 'incomplete':
                $entity->setName(IssueResolution::RESOLUTION_INCOMPLETE);
                return;
            case 'invalid':
                $entity->setName(IssueResolution::RESOLUTION_INVALID);
                return;
            case 'fixed':
                $entity->setName(IssueResolution::RESOLUTION_FIXED);
                return;
            case 'worksforme':
                $entity->setName(IssueResolution::RESOLUTION_WORKSFORME);
                return;
        }

        parent::fillEntityData($key, $entity);
    }
}
