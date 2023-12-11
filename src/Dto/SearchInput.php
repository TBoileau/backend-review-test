<?php

declare(strict_types=1);

namespace App\Dto;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class SearchInput
{
    public function __construct(
        #[NotBlank]
        public readonly DateTimeInterface $date,
        public readonly string $keyword = ''
    ) {
    }
}
