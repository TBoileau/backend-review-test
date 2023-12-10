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
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: '`event`', indexes: [new Index(columns: ['type'], name: 'IDX_EVENT_TYPE')])]
class Event
{
    #[Id]
    #[Column(type: Types::BIGINT)]
    #[GeneratedValue(strategy: 'NONE')]
    private int $id;

    #[Column(type: 'EventType')]
    private string $type;

    #[Column]
    private int $count = 1;

    #[ManyToOne(targetEntity: Actor::class, cascade: ['persist'])]
    #[JoinColumn(name: 'actor_id', referencedColumnName: 'id')]
    private Actor $actor;

    #[ManyToOne(targetEntity: Repo::class, cascade: ['persist'])]
    #[JoinColumn(name: 'repo_id', referencedColumnName: 'id')]
    private Repo $repo;

    #[Column(type: Types::JSON, options: ['jsonb' => true])]
    private array $payload;

    #[Column(type: 'datetime_immutable')]
    private DateTimeInterface $createAt;

    #[Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    public function __construct(int $id, string $type, Actor $actor, Repo $repo, array $payload, DateTimeInterface $createAt, ?string $comment)
    {
        $this->id = $id;
        EventType::assertValidChoice($type);
        $this->type = $type;
        $this->actor = $actor;
        $this->repo = $repo;
        $this->payload = $payload;
        $this->createAt = $createAt;
        $this->comment = $comment;

        if ($type === EventType::COMMIT) {
            $this->count = $payload['size'] ?? 1;
        }
    }

    public function id(): int
    {
        return $this->id;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function actor(): Actor
    {
        return $this->actor;
    }

    public function repo(): Repo
    {
        return $this->repo;
    }

    public function payload(): array
    {
        return $this->payload;
    }

    public function createAt(): DateTimeInterface
    {
        return $this->createAt;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}
