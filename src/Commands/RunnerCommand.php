<?php

namespace App\Commands;

use App\Services\RunServices;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'husky:run',
    description: 'Run Git Hooks'
)]
class RunnerCommand extends Command
{
    protected function configure()
    {
        $this->addArgument('hookName', InputArgument::REQUIRED, 'Which hook trigger run');
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
        $runServices = new RunServices($input, $output);
        $runServices->run();

        return Command::SUCCESS;
    }
}
