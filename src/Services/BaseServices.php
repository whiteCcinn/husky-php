<?php

namespace App\Services;

use App\Util\FileSystem;
use App\Util\Util;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseServices implements BaseServicesInterface
{
    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;

    /** @var Filesystem */
    protected $fs;

    protected $conf = [
        'hooks' => [
            'pre-commit' => 'husky-default-pre-commit'
        ]
    ];

    protected $binFile = 'husky-php';

    protected $huskyDir;

    protected $userDir;

    protected $phpDir;

    protected $composerPath;

    protected $composerJson;

    const CONFIG_FILE_NAME = ['.huskyrc.json', '.huskyrc'];

    const GIT_DIRECTORY_NAME = '.git';

    const GIT_HOOK_DIRECTORY_NAME = 'hooks';

    const U_MASK = 0755;

    const HOOK_LIST = [
        'Applypatch-msg',
        'pre-Applypatch',
        'post-Applypatch',
        'pre-commit',
        'prepare-commit-msg',
        'commit-msg',
        'post-commit',
        'pre-rebase',
        'post-checkout',
        'post-merge',
        'pre-push',
        'pre-receive',
        'update',
        'post-receive',
        'post-update',
        'push-to-checkout',
        'pre-auto-gc',
        'post-rewrite',
        'sendemail-validate'
    ];

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->fs = new FileSystem();
        $this->huskyDir = Util::getDirName(__DIR__, 2);
        $this->userDir = Util::getUserDir();

        $composerPath = $this->composerPath = Util::getFileOrDirPath($this->userDir, 'composer.json');
        if (is_array($composerPath)) {
            $this->output->writeln([
                'Can\'t find composer.json, skipping Git hooks installation.',
                'Please check that your project has a composer.json or create it and reinstall husky-php.'
            ]);

            exit(1);
        }

        $this->phpDir = Util::getDirName($composerPath);

        foreach ($this->conf['hooks'] as $hook => &$defaultCommand) {
            $defaultCommand = Util::getFileOrDirPath($this->userDir, $defaultCommand);
        }
        unset($defaultCommand);

        $composerJson =json_decode($this->fs->readFileSync($composerPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $composerJson = [];
        }

        $this->composerJson = $composerJson;

        if (isset($composerJson['config']['vendor-dir'])) {
            Util::$vendor = $composerJson['config']['vendor-dir'];
        }

        if (isset($composerJson['config']['bin-dir'])) {
            Util::$vendor = $composerJson['config']['bin-dir'];
        }
    }

    abstract protected function execute();

    protected function before()
    {
    }

    protected function after()
    {
    }

    public function run()
    {
        $this->before();
        $this->execute();
        $this->after();
    }

}