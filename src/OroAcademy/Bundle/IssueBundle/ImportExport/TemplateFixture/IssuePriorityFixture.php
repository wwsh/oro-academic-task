<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\ImportExport\TemplateFixture;

use Oro\Bundle\ImportExportBundle\TemplateFixture\AbstractTemplateRepository;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateFixtureInterface;
use OroAcademy\Bundle\IssueBundle\Entity\IssuePriority;

class IssuePriorityFixture extends AbstractTemplateRepository implements TemplateFixtureInterface
{
    /**
     * @param string $key
     * @return IssuePriority
     */
    protected function createEntity($key)
    {
        return new IssuePriority();
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return 'OroAcademy\Bundle\IssueBundle\Entity\IssuePriority';
    }

    /**
     * @return \Iterator
     */
    public function getData()
    {
        return $this->getEntityData('normal');
    }

    /**
     * @param string $key
     * @param object $entity
     */
    public function fillEntityData($key, $entity)
    {
        switch ($key) {
            case 'normal':
                $entity->setName(IssuePriority::PRIORITY_NORMAL);
                return;
            case 'low':
                $entity->setName(IssuePriority::PRIORITY_LOW);
                return;
            case 'high':
                $entity->setName(IssuePriority::PRIORITY_HIGH);
                return;
        }

        parent::fillEntityData($key, $entity);
    }
}
