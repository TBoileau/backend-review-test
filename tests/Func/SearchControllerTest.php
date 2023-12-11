<?php

declare(strict_types=1);

namespace App\Tests\Func;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SearchControllerTest extends WebTestCase
{
    private static $client;

    protected function setUp(): void
    {
        static::$client = static::createClient();
    }

    public function testSearchShouldReturnMetaAndData(): void
    {
        $client = static::$client;

        $client->request(
            'GET',
            '/api/search',
            ['date' => '2021-01-01', 'keyword' => 'test'],
            [],
            ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'],
        );

        $this->assertResponseStatusCodeSame(200);

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertSame([
            'meta' => [
                'totalEvents' => 0,
                'totalPullRequests' => 0,
                'totalCommits' => 0,
                'totalComments' => 0,
            ],
            'data' => [
                'events' => [],
                'stats' => array_fill(0, 24, ['commit' => 0, 'pullRequest' => 0, 'comment' => 0])
            ]
        ], $response);
    }

    public function testUpdateShouldReturnHttpNotFoundResponse()
    {
        $client = static::$client;

        $client->request(
            'GET',
            '/api/search',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json']
        );

        $this->assertResponseStatusCodeSame(404);
    }
}
