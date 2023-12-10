<?php

declare(strict_types=1);

namespace App\Message;

use Countable;

final class BatchEvents implements Countable
{
    public array $events = [];

    public function addEvent(array $event): void
    {
        $this->events[] = $event;
    }

    public function count(): int
    {
        return count($this->events);
    }
}
