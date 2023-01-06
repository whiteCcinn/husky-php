<?php

namespace App\Services;

use App\Util\Util;

class InstallServices extends BaseServices
{
    private array $hooks = [];

    private string $script = '';

    protected function before(): void
    {
        $this->io->title('husky > setting up git hooks');

        $userDir = $this->userDir;

        $gitDir = $userDir . DIRECTORY_SEPARATOR . self::GIT_DIRECTORY_NAME;
        if (!\is_dir($gitDir)) {
            $this->io->error([
                'Can\'t find .git, skipping Git hooks installation.',
                'Please check that you\'re in a cloned repository',
                'or run \'git init\' to create an empty Git repository and reinstall husky.',
            ]);

            exit(1);
        }

        $hookDir = $gitDir . DIRECTORY_SEPARATOR . self::GIT_HOOK_DIRECTORY_NAME;
        if (!\is_dir($gitDir . DIRECTORY_SEPARATOR . self::GIT_HOOK_DIRECTORY_NAME)) {
            \mkdir($hookDir, self::U_MASK);
        }

        $this->hooks = \array_map(fn ($item) => $hookDir . DIRECTORY_SEPARATOR . $item, self::HOOK_LIST);

        $this->script = Util::getScript($this->fs->makePathRelative($this->huskyDir, $userDir) . $this->binFile);

        // default pre-commit
        $this->fs->writeFileSync($this->conf['hooks']['pre-commit'], \str_replace(
            '{{BIN_PATH}}',
            $this->fs->makePathRelative(
                $this->phpDir . DIRECTORY_SEPARATOR . Util::$vendor,
                $userDir
            ) . 'bin',
            $this->fs->readFileSync($this->conf['hooks']['pre-commit'])
        ));

        $this->fs->writeFileSync($this->conf['hooks']['pre-commit'], \str_replace(
            '{{PHP_PROJECT_PATH}}',
            $this->phpDir,
            $this->fs->readFileSync($this->conf['hooks']['pre-commit'])
        ));

        if (isset($this->composerJson['require']['php'])) {
            \preg_match('/(?P<php_version>\d+(\.?\d)*$)/', $this->composerJson['require']['php'], $match);
            if ($match !== []) {
                $phpVersion    = $match['php_version'];
                $phpVersionArr = \explode('.', $phpVersion);
                switch (\count($phpVersionArr)) {
                    case 1:
                        $phpVersionArr[] = 0;

                        break;
                    case 2:
                        break;
                    default:
                        $phpVersionArr = \array_slice($phpVersionArr, 0, 2);
                }
                $phpVersion = \implode('', $phpVersionArr);
                $this->fs->writeFileSync($this->conf['hooks']['pre-commit'], \str_replace(
                    '{{PHP_VERSION}}',
                    $phpVersion,
                    $this->fs->readFileSync($this->conf['hooks']['pre-commit'])
                ));
            }
        }

        // If Window Os, We need copy php-cs-fixer-config to composer bin dir
        if (\mb_strtoupper(\mb_substr(PHP_OS, 0, 3)) === 'WIN') {
            $phpCsFixerPath = Util::getFileOrDirPath($this->phpDir, 'php-cs-fixer-config', true);

            if (\is_array($phpCsFixerPath)) {
                $this->io->error([
                    'Can\'t find php-cs-fixer-config, Please add php-cs-fixer-config into composer.json',
                ]);

                exit(1);
            }

            $needCopyFiles = Util::getAllFilesByPattern($phpCsFixerPath, '/^\.php\d{2}_cs$/');

            foreach ($needCopyFiles as $file) {
                $this->fs->copy(
                    $phpCsFixerPath . DIRECTORY_SEPARATOR . $file,
                    $this->phpDir . DIRECTORY_SEPARATOR . Util::$vendor . DIRECTORY_SEPARATOR . Util::$binDir . DIRECTORY_SEPARATOR . $file,
                    true
                );
            }
        }
    }

    protected function execute(): void
    {
        $dryRun = $this->input->getOption('dry-run');

        if (!$dryRun) {
            $this->createHooks();
        }
    }

    protected function after(): void
    {
        $this->io->writeln('husky > done');
    }

    private function createHooks(): void
    {
        \array_map(function ($hook) {
            $this->createHook($hook);
        }, $this->hooks);
    }

    private function createHook(string $filename)
    {
        $name = \basename($filename);

        if ($this->fs->exists($filename)) {
            if (Util::ishusky(\file_get_contents($filename))) {
                $this->writeHook($filename);
            }

            $this->io->warning("skipping existing user hook: ${name}");

            return;
        }
        $this->writeHook($filename);
    }

    private function writeHook(string $filename)
    {
        $this->fs->writeFileSync($filename, $this->script);
        $this->fs->chmod($filename, self::U_MASK);
    }
}
