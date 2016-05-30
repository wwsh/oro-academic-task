<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\ImportExport\TemplateFixture;

use Oro\Bundle\ImportExportBundle\TemplateFixture\AbstractTemplateRepository;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateFixtureInterface;
use OroAcademy\Bundle\IssueBundle\Entity\IssueType;

/**
 * Class IssueTypeFixture
 *
 * @package OroAcademy\Bundle\IssueBundle\ImportExport\TemplateFixture
 */
class IssueTypeFixture extends AbstractTemplateRepository implements TemplateFixtureInterface
{
    /**
     * @param string $key
     * @return IssueType
     */
    protected function createEntity($key)
    {
        return new IssueType();
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return 'OroAcademy\Bundle\IssueBundle\Entity\IssueType';
    }

    /**
     * @return \Iterator
     */
    public function getData()
    {
        return $this->getEntityData('bug');
    }

    /**
     * @param string    $key
     * @param IssueType $entity
     */
    public function fillEntityData($key, $entity)
    {
        switch ($key) {
            case 'bug':
                $entity->setName(IssueType::TYPE_BUG);
                return;
            case 'task':
                $entity->setName(IssueType::TYPE_TASK);
                return;
            case 'story':
                $entity->setName(IssueType::TYPE_STORY);
                return;
            case 'subtask':
                $entity->setName(IssueType::TYPE_SUBTASK);
                return;
        }

        parent::fillEntityData($key, $entity);
    }
}
