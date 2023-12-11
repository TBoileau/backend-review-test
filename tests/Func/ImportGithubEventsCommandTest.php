<?php

declare(strict_types=1);

namespace App\Tests\Func;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class ImportGithubEventsCommandTest extends KernelTestCase
{
    public function testShouldBeSuccessful(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('app:import-github-events');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['date' => '2023-01-01']);

        $commandTester->assertCommandIsSuccessful();

        $events = $this->getEntityManager()->getRepository(Event::class)->findAll();

        self::assertCount(96, $events);
    }

    private function truncateEntities(array $entities)
    {
        $connection = $this->getEntityManager()->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();

        foreach ($entities as $entity) {
            $query = $databasePlatform->getTruncateTableSQL(
                $this->getEntityManager()->getClassMetadata($entity)->getTableName()
            );
            $connection->executeStatement($query . ' CASCADE');
        }
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }
}
