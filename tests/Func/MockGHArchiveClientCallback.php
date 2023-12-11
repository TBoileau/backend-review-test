<?php

declare(strict_types=1);

namespace App\Tests\Func;

use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MockGHArchiveClientCallback
{
    public function __invoke($method, $url, $options): ResponseInterface
    {
        return new MockResponse(
            file_get_contents(
                sprintf(
                    '%s/Fixtures/%s',
                    __DIR__,
                    basename($url)
                )
            )
        );
    }
}
