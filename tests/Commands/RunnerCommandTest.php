<?php

namespace App\Test\Commands;

use App\Commands\RunnerCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class RunnerCommandTest extends TestCase
{
    public function testExecute()
    {
        $application = new Application();
        $application->add(new RunnerCommand());

        $runnerCommand = $application->find('husky:run');


        $this->assertInstanceOf(Command::class, $runnerCommand);

        $commandTester = new CommandTester($runnerCommand);
        $commandTester->execute(
            [
                'command'   => $runnerCommand->getName(),
                'hookName'  => 'pre-commit',
                '--dry-run' => true,
            ]
        );

        $this->assertStringContainsString(
            '[INFO] husky > pre-commit',
            $commandTester->getDisplay()
        );
    }
}
