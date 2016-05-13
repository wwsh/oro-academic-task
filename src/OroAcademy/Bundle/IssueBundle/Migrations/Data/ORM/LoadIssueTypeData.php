<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OroAcademy\Bundle\IssueBundle\Entity\IssueType;

class LoadIssueTypeData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * @var array
     */
    protected $issueTypes = [
        IssueType::TYPE_BUG     => 'Bug',
        IssueType::TYPE_STORY   => 'Story',
        IssueType::TYPE_SUBTASK => 'Subtask',
        IssueType::TYPE_TASK    => 'Task',
    ];

    /**
     * Load entities to DB
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $repo = $manager->getRepository('OroAcademyIssueBundle:IssueType');

        foreach ($this->issueTypes as $typeName => $label) {
            /** @var IssueType $issueType */
            $issueType = $repo->findOneBy([ 'name' => $typeName ]);
            if (!$issueType) {
                $issueType = new IssueType($typeName, $label);
            }

            // save
            $manager->persist($issueType);
            $manager->flush();
        }
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }
}