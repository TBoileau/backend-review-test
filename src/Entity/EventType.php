<?php

declare(strict_types=1);

namespace App\Entity;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class EventType extends AbstractEnumType
{
    public const COMMIT = 'COM';
    public const COMMENT = 'MSG';
    public const PULL_REQUEST = 'PR';

    public const EVENT_TYPES = [
        'PushEvent' => self::COMMIT,
        'CommitCommentEvent' => self::COMMENT,
        'PullRequestReviewCommentEvent' => self::COMMENT,
        'PullRequestEvent' => self::PULL_REQUEST,
    ];

    protected static array $choices = [
        self::COMMIT => 'Commit',
        self::COMMENT => 'Comment',
        self::PULL_REQUEST => 'Pull Request',
    ];

    public static function fromString(string $value): string
    {
        return self::$choices[$value];
    }
}
