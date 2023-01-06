<?php

namespace App\Test\Commands;

use App\Commands\InstallerCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class InstallerCommandTest extends TestCase
{
    public function testExecute()
    {
        $application = new Application();
        $application->add(new InstallerCommand());

        $installerCommand = $application->find('husky:install');

        $this->assertInstanceOf(Command::class, $installerCommand);

        $commandTester = new CommandTester($installerCommand);
        $commandTester->execute(
            [
                'command'   => $installerCommand->getName(),
                '--dry-run' => true,
            ]
        );

        $this->assertStringContainsString(
            'husky > setting up git hooks',
            $commandTester->getDisplay()
        );

        $this->assertStringContainsString(
            'husky > done',
            $commandTester->getDisplay()
        );
    }
}
