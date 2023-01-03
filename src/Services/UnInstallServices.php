<?php

namespace App\Services;

use App\Util\Util;

class UnInstallServices extends BaseServices
{
    private $rootDir = '';

    private $hooks = [];

    protected function before()
    {
        $this->output->writeln('husky > uninstalling up git hooks');

        $userDir = $this->userDir;

        $gitDir = $userDir . DIRECTORY_SEPARATOR . self::GIT_DIRECTORY_NAME;
        if (!\is_dir($gitDir)) {
            $this->output->writeln([
                'Can\'t find .git, skipping Git hooks installation.',
                'Please check that you\'re in a cloned repository',
                'or run \'git init\' to create an empty Git repository and reinstall husky.',
            ]);

            exit(1);
        }

        $hookDir       = $gitDir . DIRECTORY_SEPARATOR . self::GIT_HOOK_DIRECTORY_NAME;
        $this->rootDir = $userDir;

        $this->hooks = \array_map(function ($item) use ($hookDir) {
            return $hookDir . DIRECTORY_SEPARATOR . $item;
        }, self::HOOK_LIST);
    }

    protected function execute()
    {
        $this->removeHooks();
    }

    protected function after()
    {
        $this->output->writeln('husky > done');
    }

    /**
     * @return bool
     */
    private function removeHooks()
    {
        \array_map(function ($hook) {
            if ($this->canRemove($hook)) {
                $this->removeHook($hook);
            }
        }, $this->hooks);

        return true;
    }

    /**
     * @param $filename
     *
     * @return bool
     */
    private function removeHook($filename)
    {
        return @\unlink($filename);
    }

    /**
     * @param $filename
     *
     * @return bool
     */
    private function canRemove($filename)
    {
        if ($this->fs->exists($filename)) {
            return Util::isHusky(\file_get_contents($filename));
        }

        return false;
    }
}
