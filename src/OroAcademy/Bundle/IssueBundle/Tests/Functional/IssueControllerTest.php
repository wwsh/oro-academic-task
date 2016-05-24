<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Functional;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use OroAcademy\Bundle\IssueBundle\Entity\Issue;
use Symfony\Component\DomCrawler\Form;

class IssueControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient(
            [ ],
            array_merge($this->generateBasicAuthHeader(), [ 'HTTP_X-CSRF-Header' => 1 ])
        );
    }

    public function testIndex()
    {
        $this->client->request('GET', $this->getUrl('oroacademy_issue_index'));
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
    }

    public function testCreate()
    {
        $crawler = $this->client->request('GET', $this->getUrl('oroacademy_create_issue'));
        /** @var Form $form */
        $form                       = $crawler->selectButton('Save and Close')->form();
        $form['issue[summary]']     = 'Issue summary';
        $form['issue[description]'] = 'Issue description';
        $form['issue[priority]']->setValue(1); // whatever first record
        $form['issue[type]']->setValue(1);

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repo    = $manager->getRepository('OroAcademyIssueBundle:Issue');
        $issue   = $repo->findOneBy([ 'summary' => 'Issue summary' ]);
        $this->assertNotNull($issue);

        return $issue;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Issue $issue)
    {
        $id = $issue->getId();

        $crawler = $this->client->request('GET', $this->getUrl('oroacademy_update_issue', [ 'id' => $id ]));
        $result  = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        /** @var Form $form */
        $form = $crawler->selectButton('Save and Close')->form();

        $form['issue[description]'] = 'New issue description';

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        $em    = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repo  = $em->getRepository('OroAcademyIssueBundle:Issue');
        $issue = $repo->findOneBy([ 'summary' => 'Issue summary' ]);
        $this->assertNotNull($issue);
        $this->assertEquals('New issue description', $issue->getDescription());

        return $issue;
    }

    /**
     * @depends testUpdate
     */
    public function testCollaborators(Issue $issue)
    {
        $this->loadFixtures([ 'OroAcademy\Bundle\IssueBundle\Tests\Functional\DataFixtures\LoadCasualUsers' ]);

        $id = $issue->getId();

        // login as casual user
        $this->initClient(
            [ ],
            $this->generateBasicAuthHeader('dick.tracy', 'dick.tracy'),
            true
        );

        $crawler = $this->client->request('GET', $this->getUrl('oroacademy_view_issue', [ 'id' => $id ]));
        $result  = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        /** @var Form $form */
        // @todo Uncomment after Workflow's Start Progress is statically
        // @todo clickable in a functional test
//        $form = $crawler->selectButton('Start Progress')->form();
//        $this->client->followRedirects(true);
//        $crawler = $this->client->submit($form);
//
//        $result = $this->client->getResponse();
//        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repo    = $manager->getRepository('OroAcademyIssueBundle:Issue');
        $issue   = $repo->findOneBy([ 'summary' => 'Issue summary' ]);
        if (null !== $issue) {
            // deleting is tested in the API test controller
            $manager->remove($issue);
            $manager->flush(); // cleanup
        }

    }

    protected function tearDown()
    {
        parent::tearDown();

        // drop casual user
        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repo    = $manager->getRepository('OroUserBundle:User');
        $user    = $repo->findOneBy([ 'username' => 'dick.tracy' ]);
        if (null !== $user) {
            $manager->remove($user);
            $manager->flush();
        }
    }

}