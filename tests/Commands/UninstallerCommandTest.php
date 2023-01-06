<?php

namespace App\Test\Commands;

use App\Commands\UninstallerCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class UninstallerCommandTest extends TestCase
{
    public function testExecute()
    {
        $application = new Application();
        $application->add(new UninstallerCommand());

        $uninstallerCommand = $application->find('husky:uninstall');

        $this->assertInstanceOf(Command::class, $uninstallerCommand);

        $commandTester = new CommandTester($uninstallerCommand);
        $commandTester->execute(
            [
                'command'   => $uninstallerCommand->getName(),
                '--dry-run' => true,
            ]
        );

        $this->assertStringContainsString(
            'husky > uninstalling up git hooks',
            $commandTester->getDisplay()
        );

        $this->assertStringContainsString(
            '[OK] husky uninstall',
            $commandTester->getDisplay()
        );
    }
}
