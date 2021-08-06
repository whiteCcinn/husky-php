<?php

namespace App\Commands;

use App\Services\RunServices;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class RunnerCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'husky:run';

    protected function configure()
    {
        $this->setDescription('Run Hooks')
             ->addArgument('hookName', InputArgument::REQUIRED, 'which hook trigger run')
             ->addArgument('gitParams', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'gitParams');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $runServices = new RunServices($input, $output);
        $runServices->run();
        return 0;
    }
}