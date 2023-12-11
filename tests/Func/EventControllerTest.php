<?php

declare(strict_types=1);

namespace App\Tests\Func;

use App\DataFixtures\EventFixtures;
use App\Entity\Event;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

final class EventControllerTest extends WebTestCase
{
    private static $client;

    protected function setUp(): void
    {
        static::$client = static::createClient();
    }

    public function testUpdateShouldReturnEmptyResponse()
    {
        $client = static::$client;

        $client->request(
            'PUT',
            sprintf('/api/event/%d/update', EventFixtures::EVENT_1_ID),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['comment' => 'It‘s a test comment !!!!!!!!!!!!!!!!!!!!!!!!!!!'])
        );

        $this->assertResponseStatusCodeSame(204);
    }


    public function testUpdateShouldReturnHttpNotFoundResponse()
    {
        $client = static::$client;

        $client->request(
            'PUT',
            sprintf('/api/event/%d/update', 7897897897),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['comment' => 'It‘s a test comment !!!!!!!!!!!!!!!!!!!!!!!!!!!'])
        );

        $this->assertResponseStatusCodeSame(404);

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertSame('"App\Entity\Event" object not found by "Symfony\Bridge\Doctrine\ArgumentResolver\EntityValueResolver".', $response['message']);
    }

    /**
     * @dataProvider providePayloadViolations
     */
    public function testUpdateShouldReturnUnprocessableContent(array $payload, array $expectedResponse)
    {
        $client = static::$client;

        $client->request(
            'PUT',
            sprintf('/api/event/%d/update', EventFixtures::EVENT_1_ID),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'Accept' => 'application/json'],
            json_encode($payload)
        );

        self::assertResponseStatusCodeSame(422);
        $response = json_decode($client->getResponse()->getContent(), true);
        self::assertSame($expectedResponse, $response);

    }

    public function providePayloadViolations(): iterable
    {
        yield 'comment too short' => [
            ['comment' => 'short'],
            ['message' => 'This value is too short. It should have 20 characters or more.']
        ];
    }
}
