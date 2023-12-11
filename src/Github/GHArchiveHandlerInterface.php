<?php

declare(strict_types=1);

namespace App\Github;

/**
 * This manager is responsible for managing the ETL (extraction, transformation, loading) of Github archives for a given date
 */
interface GHArchiveHandlerInterface
{
    public function handle(string $date): void;
}
