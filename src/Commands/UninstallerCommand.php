<?php

namespace App\Commands;

use App\Services\UninstallServices;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'husky:uninstall',
    description: 'Uninstall husky'
)]
class UninstallerCommand extends Command
{
    protected function configure()
    {
        $this->addOption(
            'dry-run',
            null,
            InputOption::VALUE_OPTIONAL,
            'Execute the trigger as a dry run.',
            false
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $uninstallServices = new UninstallServices($input, $output);
        $uninstallServices->run();

        return Command::SUCCESS;
    }
}
