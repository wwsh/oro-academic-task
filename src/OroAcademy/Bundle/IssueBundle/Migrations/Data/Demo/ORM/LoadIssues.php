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
use OroAcademy\Bundle\IssueBundle\Entity\IssueResolution;
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

        $jsonDemoFixtureFile = __DIR__ . '/../../../../Resources/fixtures/demo-issues.json';
        
        $json = json_decode(file_get_contents($jsonDemoFixtureFile), true);

        $organization = $manager->getRepository('OroOrganizationBundle:Organization')
            ->getFirst();
        
        $previous = [];
        
        foreach ($json as $issueJson) {
            $issue = new Issue();
            $issue->setCode($issueJson['code']);
            $issue->setSummary($issueJson['summary']);
            $issue->setCreatedAt(new \DateTime());
            $issue->setUpdatedAt(new \DateTime());
            $issue->setDescription($issueJson['description']);
            $issue->setPriority($this->getIssuePriority($issueJson['priority']));
            $issue->setType($this->getIssueType($issueJson['type']));
            $issue->setReporter($this->getUser($issueJson['reporter']));
            $issue->setAssignee($this->getUser($issueJson['assignee']));
            $issue->setResolution($this->getResolution($issueJson['resolution']));
            $issue->setOrganization($organization);
            if (isset($issueJson['parent'])) {
                $issue->setParent($previous[$issueJson['parent']]);
            }
            $manager->persist($issue);
            $manager->flush();
            $previous[$issueJson['code']] = $issue;
        }
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

    protected function getResolution($resolutionName)
    {
        $repo = $this->manager->getRepository('OroAcademyIssueBundle:IssueResolution');
        return $repo->findOneBy([ 'name' => $resolutionName ]);
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