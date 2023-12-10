<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\EventType;
use App\Entity\TempEvent;
use DateTimeImmutable;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class EventDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): TempEvent
    {
        $tempEvent = new TempEvent();
        $tempEvent->eventId = (int) $data['id'];
        $tempEvent->type = EventType::EVENT_TYPES[$data['type']];
        $tempEvent->payload = $data['payload'];
        $tempEvent->createdAt = new DateTimeImmutable($data['created_at']);
        $tempEvent->actorId = (int) $data['actor']['id'];
        $tempEvent->actorLogin = $data['actor']['login'];
        $tempEvent->actorUrl = $data['actor']['url'];
        $tempEvent->actorAvatarUrl = $data['actor']['avatar_url'];
        $tempEvent->repoId = (int) $data['repo']['id'];
        $tempEvent->repoName = $data['repo']['name'];
        $tempEvent->repoUrl = $data['repo']['url'];
        if ($tempEvent->type === EventType::COMMIT) {
            $tempEvent->count = $tempEvent->payload['size'] ?? 1;
        }

        return $tempEvent;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        return $type === TempEvent::class;
    }
}
