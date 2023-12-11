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

        $container = static::getContainer();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');

        $events = $entityManager->getRepository(Event::class)->findAll();

        self::assertCount(96, $events);
    }
}
