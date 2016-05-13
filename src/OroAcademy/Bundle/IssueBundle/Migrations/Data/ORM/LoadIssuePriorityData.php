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
use OroAcademy\Bundle\IssueBundle\Entity\IssuePriority;

class LoadIssuePriorityData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var array
     */
    protected $data = [
        IssuePriority::PRIORITY_HIGH   => [ 'High', 100 ],
        IssuePriority::PRIORITY_NORMAL => [ 'Normal', 50 ],
        IssuePriority::PRIORITY_LOW    => [ 'Low', 1 ],
    ];

    /**
     * Load entities to DB
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $repo = $manager->getRepository('OroAcademyIssueBundle:IssuePriority');

        foreach ($this->data as $name => $value) {
            /** @var IssuePriority $issuePriority */
            $issuePriority = $repo->findOneBy([ 'name' => $name ]);
            if (!$issuePriority) {
                $issuePriority = new IssuePriority($name, $value[0], $value[1]);
            }

            // save
            $manager->persist($issuePriority);
            $manager->flush();
        }
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
}