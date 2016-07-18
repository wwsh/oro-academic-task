<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\ImportExport\TemplateFixture;

use Oro\Bundle\ImportExportBundle\TemplateFixture\AbstractTemplateRepository;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateFixtureInterface;
use OroAcademy\Bundle\IssueBundle\Entity\Issue;

class IssueFixture extends AbstractTemplateRepository
    implements TemplateFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    protected function createEntity($key)
    {
        return new Issue();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityClass()
    {
        return 'OroAcademy\Bundle\IssueBundle\Entity\Issue';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->getEntityData('ABC-123');
    }

    /**
     * @param string $key
     * @param Issue  $entity
     */
    public function fillEntityData($key, $entity)
    {
        $priorityRepo     = $this->templateManager
            ->getEntityRepository('OroAcademy\Bundle\IssueBundle\Entity\IssuePriority');
        $typeRepo         = $this->templateManager
            ->getEntityRepository('OroAcademy\Bundle\IssueBundle\Entity\IssueType');
        $resolutionRepo   = $this->templateManager
            ->getEntityRepository('OroAcademy\Bundle\IssueBundle\Entity\IssueResolution');
        $userRepo         = $this->templateManager
            ->getEntityRepository('Oro\Bundle\UserBundle\Entity\User');
        $organizationRepo = $this->templateManager
            ->getEntityRepository('Oro\Bundle\OrganizationBundle\Entity\Organization');

        switch ($key) {
            case 'ABC-123':
                $entity->setCode('ABC-123');
                $entity->setSummary('Oro Academical Task Summary');
                $entity->setDescription('The Description');
                $entity->setCreatedAt(new \DateTime());
                $entity->setAssignee($userRepo->getEntity('John Doo'));
                $entity->setReporter($userRepo->getEntity('John Doo'));
                $entity->setPriority($priorityRepo->getEntity('normal'));
                $entity->setType($typeRepo->getEntity('bug'));
                $entity->setResolution($resolutionRepo->getEntity('incomplete'));
                $entity->setOrganization($organizationRepo->getEntity('default'));
                return;
        }

        parent::fillEntityData($key, $entity);
    }
}
