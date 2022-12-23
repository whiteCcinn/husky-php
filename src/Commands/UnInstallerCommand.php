<?php

namespace App\Commands;

use App\Services\UnInstallServices;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UnInstallerCommand extends Command
{
    protected static $defaultName = 'husky:uninstall';

    protected function configure()
    {
        $this->setDescription('UnInstall husky-php');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $installServices = new UnInstallServices($input, $output);
        $installServices->run();

        return 0;
    }
}
