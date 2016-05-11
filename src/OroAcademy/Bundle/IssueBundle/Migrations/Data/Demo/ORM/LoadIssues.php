<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OroAcademy\Bundle\IssueBundle\Entity\Issue;
use OroAcademy\Bundle\IssueBundle\Entity\IssuePriority;
use OroAcademy\Bundle\IssueBundle\Entity\IssueType;

class LoadIssues extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $adminUser = $this->getUser('admin');

        $issue = new Issue();
        $issue->setCode('ABC-123');
        $issue->setSummary('Oro Academical Task');
        $issue->setCreatedAt(new \DateTime());
        $issue->setUpdatedAt(new \DateTime());
        $issue->setDescription('An example academical issue, reported within the academical task');
        $issue->setPriority($this->getIssuePriority(IssuePriority::PRIORITY_HIGH));
        $issue->setType($this->getIssueType(IssueType::TYPE_TASK));
        $issue->setReporter($adminUser);
        $issue->setAssignee($adminUser);
        $issue->setResolution(''); // todo
        $issue->setStatus(''); // todo

        $manager->persist($issue);
        $manager->flush();
    }

    protected function getIssuePriority($priorityName)
    {
        $repo = $this->manager->getRepository('OroAcademyIssueBundle:IssuePriority');
        return $repo->findOneBy([ 'name' => $priorityName ]);
    }

    protected function getIssueType($typeName)
    {
        $repo = $this->manager->getRepository('OroAcademyIssueBundle:IssueType');
        return $repo->findOneBy([ 'name' => $typeName ]);
    }

    protected function getUser($string)
    {
        $repo = $this->manager->getRepository('OroUserBundle:User');
        return $repo->findOneBy([ 'username' => $string ]);
    }

    /**
     * Lower than 100 values reserved for main fixtures.
     *
     * @return int
     */
    public function getOrder()
    {
        return 100;
    }
}