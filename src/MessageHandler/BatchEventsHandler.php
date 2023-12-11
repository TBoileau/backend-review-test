<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\EventType;
use App\Entity\TempEvent;
use App\Message\BatchEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

#[AsMessageHandler]
class BatchEventsHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DenormalizerInterface $denormalizer
    ) {
    }

    public function __invoke(BatchEvents $batchEvents): void
    {
        $index = 0;

        $this->entityManager->getConnection()->getConfiguration()->setMiddlewares([]);

        foreach ($batchEvents->events as $eventRaw) {
            if ($eventRaw === null) {
                return;
            }

            if (!isset(EventType::EVENT_TYPES[$eventRaw['type']])) {
                continue;
            }

            $tempEvent = $this->denormalizer->denormalize($eventRaw, TempEvent::class);

            $this->entityManager->persist($tempEvent);

            if (++$index % 500 === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }

        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
