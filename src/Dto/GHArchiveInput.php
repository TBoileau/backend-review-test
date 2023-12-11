<?php

declare(strict_types=1);

namespace App\Dto;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;

class GHArchiveInput
{
    public function __construct(
        #[NotBlank]
        #[Date]
        public string $date
    ) {
    }

    /**
     * Returns the date and time period of a given day
     *
     * @return iterable<DateTimeImmutable>
     */
    public function getDateRange(): iterable
    {
        $startDate = (new DateTimeImmutable($this->date))->setTime(0, 0);
        $endDate = $startDate->add(new \DateInterval('P1D'));
        return new DatePeriod($startDate, new DateInterval('PT1H'), $endDate);
    }
}
