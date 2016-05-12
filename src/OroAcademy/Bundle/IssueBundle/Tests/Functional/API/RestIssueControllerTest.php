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

    public function testCreate()
    {
        $issue = [
            "issue" => [
                "code"        => 'EGH-' . rand(1, 200),
                "summary"     => "Test summary",
                "description" => "Test description",
                "type"        => "bug",
                "priority"    => "high",
                "reporter"    => "admin",
            ]
        ];

        $this->client->request(
            'POST',
            $this->getUrl('oroacademy_api_post_issue'),
            $issue
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 201);
        $this->assertArrayHasKey('id', $result);

        return $issue;
    }

    /**
     * @param array $issue
     * @depends testCreate
     * @return array
     */
    public function testGet(array $issue)
    {
        $this->client->request(
            'GET',
            $this->getUrl('oroacademy_api_get_issues')
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $id     = $issue['id'];
        $result = array_filter(
            $result,
            function ($a) use ($id) {
                return $a['id'] == $id;
            }
        );

        $this->assertNotEmpty($result);
        $this->assertEquals($issue['issue']['code'], reset($result)['code']);

        $this->client->request(
            'GET',
            $this->getUrl('oroacademy_api_get_issue', [ 'id' => $issue['id'] ])
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertEquals($issue['issue']['code'], $result['code']);
        $this->assertTrue(array_key_exists('lifetimeValue', $result));
    }

    /**
     * @param array $issue
     * @depends testCreate
     * @depends testGet
     */
    public function testUpdate(array $issue)
    {
        $issue['account']['code'] .= "_Updated";
        $this->client->request(
            'PUT',
            $this->getUrl('oroacademy_api_put_issue', [ 'id' => $issue['id'] ]),
            $issue
        );
        $result = $this->client->getResponse();

        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->request(
            'GET',
            $this->getUrl('oroacademy_api_get_issue', [ 'id' => $issue['id'] ])
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertEquals(
            $issue['issue']['code'],
            $result['code']
        );
    }

    /**
     * @depends testCreate
     */
    public function testList()
    {
        $this->client->request(
            'GET',
            $this->getUrl('oroacademy_api_get_issuesa')
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);
        $this->assertEquals(1, count($result));
    }

}