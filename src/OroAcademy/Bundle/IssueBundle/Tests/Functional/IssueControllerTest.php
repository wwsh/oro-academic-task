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
        $form['issue[reporter]']->setValue(1); // admin user
        $form['issue[priority]']->setValue(1); // whatever first record
        $form['issue[type]']->setValue(1);

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        $em    = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repo  = $em->getRepository('OroAcademyIssueBundle:Issue');
        $issue = $repo->findOneBy([ 'summary' => 'Issue summary' ]);
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
        // deleting is tested in the API test controller
        $em->remove($issue);
        $em->flush(); // cleanup
    }
}