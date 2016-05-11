<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Functional\API;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * Class RestIssueControllerTest
 * @package OroAcademy\Bundle\IssueBundle\Tests\Functional\API
 * @outputBuffering enabled
 * @dbIsolation
 */
class RestIssueControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient([ ], $this->generateWsseAuthHeader());
        $this->loadFixtures([ 'OroAcademy\Bundle\IssueBundle\Tests\Functional\DataFixtures\LoadIssueData' ]);
    }

    public function testDelete()
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $repo  = $em->getRepository('OroAcademyIssueBundle:Issue');
        $issue = $repo->findOneBy([ 'code' => 'XYZ-123' ]);
        $id    = $issue->getId();

        $this->client->request(
            'DELETE',
            $this->getUrl('oroacademy_api_delete_issue', [ 'id' => $id ])
        );
        $result = $this->client->getResponse();
        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $issue = $repo->findOneBy([ 'code' => 'XYZ-123' ]);
        $this->assertNull($issue);
    }
}