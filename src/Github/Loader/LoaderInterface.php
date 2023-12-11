<?php

declare(strict_types=1);

namespace App\Github\Loader;

/**
 * This interface is responsible for loading events into the persistent storage
 */
interface LoaderInterface
{
    /**
     * @param array{
     *          id: int,
     *          type: string,
     *          created_at: string,
     *          payload: array<string, mixed>,
     *          actor: array{
     *              id: int,
     *              login: string,
     *              url: string,
     *              avatar_url: string,
     *          },
     *          repo: array{
     *              id: int,
     *              name: string,
     *              url: string,
     *          }
     *      } $event
     */
    public function register(array $event): void;

    public function load(bool $force = false): void;
}
