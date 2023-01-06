<?php

namespace App\Services;

use App\Util\FileSystem;
use App\Util\Util;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class BaseServices implements BaseServicesInterface
{
    protected InputInterface $input;

    protected OutputInterface $output;

    protected SymfonyStyle $io;

    protected FileSystem $fs;

    protected array $conf = [
        'hooks' => [
            'pre-commit' => 'husky-default-pre-commit',
        ],
    ];

    protected string $binFile = 'husky';

    protected string $huskyDir;

    protected string $userDir;

    protected string $phpDir;

    protected string|array $composerPath;

    protected mixed $composerJson;

    public const CONFIG_FILE_NAME = ['.huskyrc.json', '.huskyrc'];

    public const GIT_DIRECTORY_NAME = '.git';

    public const GIT_HOOK_DIRECTORY_NAME = 'hooks';

    public const U_MASK = 0755;

    public const HOOK_LIST = [
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
        'sendemail-validate',
    ];

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input       = $input;
        $this->output      = $output;
        $this->io          = new SymfonyStyle($input, $output);
        $this->fs          = new FileSystem();
        $this->huskyDir    = Util::getDirName(__DIR__, 2);
        $this->userDir     = Util::getUserDir();

        $composerPath = $this->composerPath = Util::getFileOrDirPath($this->userDir, 'composer.json');
        if (\is_array($composerPath)) {
            $this->io->error([
                'Can\'t find composer.json, skipping Git hooks installation.',
                'Please check that your project has a composer.json or create it and reinstall husky.',
            ]);

            exit(1);
        }

        $this->phpDir = Util::getDirName($composerPath);

        foreach ($this->conf['hooks'] as $hook => &$defaultCommand) {
            $defaultCommand = Util::getFileOrDirPath($this->userDir, $defaultCommand);
        }
        unset($defaultCommand);

        $composerJson = \json_decode($this->fs->readFileSync($composerPath), true);

        if (\json_last_error() !== JSON_ERROR_NONE) {
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

    protected function before(): void
    {
    }

    protected function after(): void
    {
    }

    public function run(): void
    {
        $this->before();
        $this->execute();
        $this->after();
    }
}
