<?php

declare(strict_types=1);

namespace App\Github;

use App\Dto\GHArchiveInput;
use App\Github\Extractor\ExtractorInterface;
use App\Github\Loader\LoaderInterface;
use App\Github\Transformer\TransformerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class GHArchiveHandler implements GHArchiveHandlerInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly ExtractorInterface $extractor,
        private readonly TransformerInterface $transformer,
        private readonly LoaderInterface $loader
    ) {
    }

    public function handle(string $date): void
    {
        $input = new GHArchiveInput($date);

        if ($this->validator->validate($input)->count() > 0) {
            throw new \InvalidArgumentException('Invalid date');
        }

        foreach ($this->extractor->extract($input) as $filename) {
            foreach ($this->transformer->transform($filename) as $event) {
                $this->loader->register($event);
            }
            unlink($filename);
        }

        $this->loader->load(true);
    }
}
