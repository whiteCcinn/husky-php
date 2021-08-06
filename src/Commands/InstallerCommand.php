<?php

namespace App\Commands;

use App\Services\InstallServices;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class InstallerCommand extends Command
{
    protected static $defaultName = 'husky:install';

    protected function configure()
    {
        $this
            ->setDescription('Install husky-php')
            ->setHelp('This command Similar to js-husky client git hook implementation');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $installServices = new InstallServices($input, $output);
        $installServices->run();
        return 0;
    }
}