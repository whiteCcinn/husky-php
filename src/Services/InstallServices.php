<?php

namespace App\Services;

use App\Util\Util;

class InstallServices extends BaseServices
{
    private $rootDir = '';

    private $hooks = [];

    private $script = '';

    protected function before()
    {
        $this->output->writeln('husky > setting up git hooks');

        $userDir = $this->userDir;

        $gitDir = $userDir . DIRECTORY_SEPARATOR . self::GIT_DIRECTORY_NAME;
        if (!is_dir($gitDir)) {
            $this->output->writeln([
                'Can\'t find .git, skipping Git hooks installation.',
                'Please check that you\'re in a cloned repository',
                'or run \'git init\' to create an empty Git repository and reinstall husky.'
            ]);

            exit(1);
        }

        $hookDir = $gitDir . DIRECTORY_SEPARATOR . self::GIT_HOOK_DIRECTORY_NAME;
        if (!is_dir($gitDir . DIRECTORY_SEPARATOR . self::GIT_HOOK_DIRECTORY_NAME)) {
            mkdir($hookDir, self::U_MASK);
        }

        $this->rootDir = $userDir;
        $this->hooks = array_map(function ($item) use ($hookDir) {
            return $hookDir . DIRECTORY_SEPARATOR . $item;
        }, self::HOOK_LIST);

        $this->script = Util::getScript($this->fs->makePathRelative($this->huskyDir, $this->rootDir) . $this->binFile);

        $this->fs->writeFileSync($this->conf['hooks']['pre-commit'], str_replace('{{BIN_PATH}}',
            $this->fs->makePathRelative($this->phpDir . DIRECTORY_SEPARATOR . Util::$vendor,
                $this->rootDir) . 'bin', $this->fs->readFileSync($this->conf['hooks']['pre-commit'])));

        if (isset($this->composerJson['require']['php'])) {
            preg_match('/(?P<php_version>\d+(\.?\d)*$)/', $this->composerJson['require']['php'], $match);
            if (!empty($match)) {
                $phpVersion = $match['php_version'];
                $phpVersionArr = explode('.', $phpVersion);
                switch (count($phpVersionArr)) {
                    case 1:
                        $phpVersionArr[] = 0;
                        break;
                    case 2:
                        break;
                    default:
                        $phpVersionArr = array_slice($phpVersionArr, 0, 2);
                }
                $phpVersion = implode('', $phpVersionArr);
                $this->fs->writeFileSync($this->conf['hooks']['pre-commit'], str_replace('{{PHP_VERSION}}',
                    $phpVersion
                    , $this->fs->readFileSync($this->conf['hooks']['pre-commit'])));
            }
        }

        // If Window Os, We need copy php-cs-fixer-config to composer bin dir
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $phpCsFixerPath = Util::getFileOrDirPath($this->phpDir, 'php-cs-fixer-config', true);

            if (is_array($phpCsFixerPath)) {
                $this->output->writeln([
                    'Can\'t find php-cs-fixer-config, Please add php-cs-fixer-config into composer.json',
                ]);

                exit(1);
            }

            $needCopyFiles = Util::getAllFilesByPattern($phpCsFixerPath, '/^\.php\d{2}_cs$/');

            foreach ($needCopyFiles as $file) {
                $this->fs->copy(
                    $phpCsFixerPath . DIRECTORY_SEPARATOR . $file,
                    $this->phpDir . DIRECTORY_SEPARATOR . Util::$vendor . DIRECTORY_SEPARATOR . Util::$bin_dir . DIRECTORY_SEPARATOR . $file,
                    true
                );
            }
        }
    }

    protected function execute()
    {
        $this->createHooks();
    }

    protected function after()
    {
        $this->output->writeln('husky > done');
    }

    /**
     * @return bool
     */
    private function createHooks()
    {
        array_map(function ($hook) {
            $this->createHook($hook);
        }, $this->hooks);

        return true;
    }

    /**
     * @param $filename
     */
    private function createHook($filename)
    {
        $name = basename($filename);

        if ($this->fs->exists($filename)) {
            // Update
            if (Util::isHusky(file_get_contents($filename))) {
                $this->writeHook($filename);
            }

            // Skip
            $this->output->writeln("skipping existing user hook: ${name}");

            return;
        }

        // Create hook if it doesn't exist
        $this->writeHook($filename);
    }

    /**
     * @param $filename
     */
    private function writeHook($filename)
    {
        $this->fs->writeFileSync($filename, $this->script);
        $this->fs->chmod($filename, self::U_MASK);
    }
}
