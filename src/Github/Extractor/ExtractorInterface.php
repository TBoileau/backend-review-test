<?php

declare(strict_types=1);

namespace App\Github\Extractor;

use App\Dto\GHArchiveInput;

/**
 * This extractor is responsible for extracting the Github archives for a given date
 */
interface ExtractorInterface
{
    /**
     * @param GHArchiveInput $input
     *
     * @return iterable<string>
     */
    public function extract(GHArchiveInput $input): iterable;
}
