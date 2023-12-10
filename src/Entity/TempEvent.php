<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: 'temp_events', indexes: [
    new Index(columns: ['event_id'], name: 'IDX_TEMP_EVENT_ID'),
    new Index(columns: ['type'], name: 'IDX_TEMP_EVENT_TYPE'),
    new Index(columns: ['actor_id'], name: 'IDX_TEMP_EVENT_ACTOR_ID'),
    new Index(columns: ['repo_id'], name: 'IDX_TEMP_EVENT_REPO_ID'),
])]
class TempEvent
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    public ?int $id = null;

    #[Column(type: Types::BIGINT)]
    public int $eventId;

    #[Column(type: 'EventType')]
    public string $type;

    #[Column(type: Types::JSON, options: ['jsonb' => true])]
    public array $payload;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    public DateTimeInterface $createdAt;

    #[Column]
    public int $count = 1;

    #[Column(type: Types::BIGINT)]
    public int $actorId;

    #[Column]
    public string $actorLogin;

    #[Column]
    public string $actorUrl;

    #[Column]
    public string $actorAvatarUrl;

    #[Column(type: Types::BIGINT)]
    public int $repoId;

    #[Column]
    public string $repoName;

    #[Column]
    public string $repoUrl;
}
