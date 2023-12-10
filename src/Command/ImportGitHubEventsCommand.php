<?php

declare(strict_types=1);

namespace App\Command;

use App\Github\GHArchiveHandlerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command must import GitHub events.
 * You can add the parameters and code you want in this command to meet the need.
 */
#[AsCommand(
    name: 'app:import-github-events',
    description: 'Import GH events',
)]
final class ImportGitHubEventsCommand extends Command
{
    public function __construct(private readonly GHArchiveHandlerInterface $ghArchiveHandler)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('date', InputArgument::OPTIONAL, 'Date to import', date('Y-m-d'));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = $input->getArgument('date');

        $this->ghArchiveHandler->handle($date);

        return Command::SUCCESS;
    }
}
