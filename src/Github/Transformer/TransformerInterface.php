<?php

declare(strict_types=1);

namespace App\Github\Transformer;

/**
 * This interface is responsible for transforming a Github archive file into an iterable of events
 */
interface TransformerInterface
{
    /**
     * @param string $filename
     *
     * @return iterable<
     *      array{
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
     *      }
     * >
     */
    public function transform(string $filename): iterable;
}
