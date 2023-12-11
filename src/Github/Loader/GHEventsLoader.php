<?php

declare(strict_types=1);

namespace App\Github\Loader;

use App\Message\BatchEvents;
use Symfony\Component\Messenger\MessageBusInterface;

final class GHEventsLoader implements LoaderInterface
{
    private BatchEvents $batchEvents;

    public function __construct(private readonly MessageBusInterface $messageBus)
    {
        $this->batchEvents = new BatchEvents();
    }

    public function register(array $event): void
    {
        $this->batchEvents->addEvent($event);

        unset($event);

        $this->load();
    }

    public function load(bool $force = false): void
    {
        if ($force || count($this->batchEvents) % 5000 === 0) {
            $this->messageBus->dispatch($this->batchEvents);
            $this->batchEvents = new BatchEvents();
            gc_collect_cycles();
        }
    }
}
