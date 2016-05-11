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
use OroAcademy\Bundle\IssueBundle\Entity\IssueResolution;

class LoadIssueResolutionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var array
     */
    protected $data = [
        IssueResolution::RESOLUTION_DUPLICATE,
        IssueResolution::RESOLUTION_WONTFIX,
        IssueResolution::RESOLUTION_WORKSFORME,
        IssueResolution::RESOLUTION_INVALID,
        IssueResolution::RESOLUTION_INCOMPLETE,
        IssueResolution::RESOLUTION_FIXED,
    ];


    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $repo = $manager->getRepository('OroAcademyIssueBundle:IssueResolution');

        foreach ($this->data as $name) {
            /** @var IssueResolution $issuePriority */
            $issuePriority = $repo->findOneBy([ 'name' => $name ]);
            if (!$issuePriority) {
                $issuePriority = new IssueResolution($name);
            }

            // save
            $manager->persist($issuePriority);
            $manager->flush();
        }
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 3;
    }
}