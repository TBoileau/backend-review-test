<?php

declare(strict_types=1);

namespace App\Github\Extractor;

use App\Dto\GHArchiveInput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class GHArchiveExtractor implements ExtractorInterface
{
    public function __construct(private readonly HttpClientInterface $gharchiveClient)
    {
    }

    public function extract(GHArchiveInput $input): iterable
    {
        /** @var ResponseInterface[] $responses */
        $responses = [];

        foreach ($input->getDateRange() as $date) {
            $responses[] = $this->gharchiveClient->request(
                'GET',
                sprintf('/%s.json.gz', $date->format('Y-m-d-G')),
                [
                    'headers' => [
                        'Accept-Encoding' => 'gzip',
                    ],
                ]
            );

            foreach ($this->gharchiveClient->stream($responses) as $response => $chunk) {
                if ($chunk->isLast()) {
                    if (Response::HTTP_OK !== $response->getStatusCode()) {
                        continue;
                    }

                    $url = $response->getInfo('url');

                    $filename = sprintf('%s/%s', sys_get_temp_dir(), basename($url));

                    (new Filesystem())->dumpFile($filename, $response->getContent());

                    yield $filename;

                    unset($response, $chunk, $url, $filename);
                }
            }
        }
    }
}
