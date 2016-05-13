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
    }

    public function testCreate()
    {
        $issue = [
            "issue" => [
                "code"        => 'EGH-123',
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

        return [ 'id' => $result['id'], 'issue' => $issue['issue'] ];
    }

    /**
     * @depends testCreate
     */
    public function testList()
    {
        $this->client->request(
            'GET',
            $this->getUrl('oroacademy_api_get_issues')
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);
        $this->assertEquals(1, count($result));
    }

    /**
     * @param array $data
     * @return array
     * @depends testCreate
     */
    public function testGet(array $data)
    {
        $id    = $data['id'];
        $issue = $data['issue'];

        $this->client->request(
            'GET',
            $this->getUrl('oroacademy_api_get_issues')
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $result = array_filter(
            $result,
            function ($a) use ($id) {
                return $a['id'] == $id;
            }
        );

        $this->assertNotEmpty($result);
        $this->assertEquals($issue['code'], reset($result)['code']);

        $this->client->request(
            'GET',
            $this->getUrl('oroacademy_api_get_issue', [ 'id' => $id ])
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertEquals($issue['code'], $result['code']);
    }

    /**
     * @param array $data
     * @depends testCreate
     * @depends testGet
     */
    public function testUpdate(array $data)
    {
        $id    = $data['id'];
        $issue = $data['issue'];

        $issue['code'] .= "_Updated";
        $this->client->request(
            'PUT',
            $this->getUrl('oroacademy_api_put_issue', [ 'id' => $id ]),
            [ 'issue' => $issue ]
        );
        $result = $this->client->getResponse();

        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->request(
            'GET',
            $this->getUrl('oroacademy_api_get_issue', [ 'id' => $id ])
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertEquals(
            $issue['code'],
            $result['code']
        );
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $data)
    {
        $id    = $data['id'];
        $issue = $data['issue'];

        $this->client->request(
            'DELETE',
            $this->getUrl('oroacademy_api_delete_issue', [ 'id' => $id ])
        );
        $result = $this->client->getResponse();
        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $repo = $this->getContainer()->get('doctrine.orm.entity_manager')
                     ->getRepository('OroAcademyIssueBundle:Issue');

        $issue = $repo->findOneBy([ 'code' => $issue['code'] ]);
        $this->assertNull($issue);
    }

}