<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\SearchInput;
use App\Entity\EventType;
use Doctrine\DBAL\Connection;
use function Symfony\Component\String\u;

final class DbalReadEventRepository implements ReadEventRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function countAll(SearchInput $searchInput): int
    {
        $sql = <<<SQL
        SELECT sum(count) as count
        FROM event
        WHERE date(create_at) = :date
        AND payload::text like :keyword
SQL;

        return (int) $this->connection->fetchOne($sql, [
            'date' => $searchInput->date->format('Y-m-d'),
            'keyword' => '%' . $searchInput->keyword . '%',
        ]);
    }

    public function countByType(SearchInput $searchInput): array
    {
        $sql = <<<'SQL'
            SELECT type, sum(count) as count
            FROM event
            WHERE date(create_at) = :date
            AND payload::text like :keyword
            GROUP BY type
SQL;

        return $this->connection->fetchAllKeyValue($sql, [
            'date' => $searchInput->date->format('Y-m-d'),
            'keyword' => '%' . $searchInput->keyword . '%',
        ]);
    }

    public function statsByTypePerHour(SearchInput $searchInput): array
    {
        $sql = <<<SQL
            SELECT extract(hour from create_at) as hour, type, sum(count) as count
            FROM event
            WHERE date(create_at) = :date
            AND payload::text like :keyword
            GROUP BY TYPE, EXTRACT(hour from create_at)
SQL;

        $stats = $this->connection->fetchAllAssociative($sql, [
            'date' => $searchInput->date->format('Y-m-d'),
            'keyword' => '%' . $searchInput->keyword . '%',
        ]);

        $data = array_fill(0, 24, ['commit' => 0, 'pullRequest' => 0, 'comment' => 0]);

        foreach ($stats as $stat) {
            $eventType = u(EventType::fromString($stat['type']))->camel()->toString();
            $data[(int) $stat['hour']][$eventType] = $stat['count'];
        }

        return $data;
    }

    public function getLatest(SearchInput $searchInput): array
    {
        $sql = <<<SQL
            SELECT type, payload
            FROM event
            WHERE date(create_at) = :date
            AND payload::text like :keyword
SQL;

        $result = $this->connection->fetchAllAssociative($sql, [
            'date' => $searchInput->date->format('Y-m-d'),
            'keyword' => '%' . $searchInput->keyword . '%',
        ]);

        $result = array_map(static function($item) {
            $item['payload'] = json_decode($item['payload'], true);

            return $item;
        }, $result);

        return $result;
    }
}
