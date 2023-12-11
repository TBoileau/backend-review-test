<?php

declare(strict_types=1);

namespace App\Tests\Unit\MessageHandler;

use App\Entity\TempEvent;
use App\Message\BatchEvents;
use App\MessageHandler\BatchEventsHandler;
use App\Serializer\EventDenormalizer;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class BatchEventsHandlerTest extends TestCase
{
    public function testShouldHandleBatchEvents(): void
    {
        $batchEvents = new BatchEvents();

        $events = array_fill(0, 1000, [
            'id' => 1,
            'type' => 'PushEvent',
            'created_at' => '2021-01-01T00:00:00+00:00',
            'payload' => [],
            'actor' => [
                'id' => 1,
                'login' => 'test',
                'url' => 'https://github.com',
                'avatar_url' => 'https://github.com',
            ],
            'repo' => [
                'id' => 1,
                'name' => 'test',
                'url' => 'https://github.com',
            ]
        ]);

        array_walk($events, [$batchEvents, 'addEvent']);

        $denormalizer = new EventDenormalizer();

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $connection = $this->createMock(Connection::class);

        $configuration = $this->createMock(Configuration::class);

        $configuration->expects(self::once())
            ->method('setMiddlewares')
            ->with([]);

        $connection->expects(self::once())
            ->method('getConfiguration')
            ->willReturn($configuration);

        $entityManager
            ->expects(self::once())
            ->method('getConnection')
            ->willReturn($connection);

        $entityManager
            ->expects(self::exactly(1000))
            ->method('persist')
            ->with(self::isInstanceOf(TempEvent::class))
        ;

        $entityManager->expects(self::exactly(3))->method('flush');

        $entityManager->expects(self::exactly(3))->method('clear');

        $handler = new BatchEventsHandler($entityManager, $denormalizer);

        $handler->__invoke($batchEvents);
    }
}
