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
        IssueResolution::RESOLUTION_DUPLICATE  => 'Duplicate',
        IssueResolution::RESOLUTION_WONTFIX    => 'Won\'t Fix',
        IssueResolution::RESOLUTION_WORKSFORME => 'Works For Me',
        IssueResolution::RESOLUTION_INVALID    => 'Invalid',
        IssueResolution::RESOLUTION_INCOMPLETE => 'Incomplete',
        IssueResolution::RESOLUTION_FIXED      => 'Fixed',
    ];


    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $repo = $manager->getRepository('OroAcademyIssueBundle:IssueResolution');

        foreach ($this->data as $name => $label) {
            /** @var IssueResolution $issuePriority */
            $issuePriority = $repo->findOneBy([ 'name' => $name ]);
            if (!$issuePriority) {
                $issuePriority = new IssueResolution($name, $label);
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