<?php

declare(strict_types=1);

namespace App\Github\Transformer;

final class GHArchiveTransformer implements TransformerInterface
{
    public function transform(string $filename): iterable
    {
        $stream = gzopen($filename, 'r');

        while (!gzeof($stream)) {
            $line = gzgets($stream, 4096);

            $event = json_decode($line, true);

            if (JSON_ERROR_NONE !== json_last_error()) {
                continue;
            }

            yield $event;
        }

        gzclose($stream);
    }
}
