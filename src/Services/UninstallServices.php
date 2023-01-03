<?php

namespace App\Services;

use App\Util\Util;

class UninstallServices extends BaseServices
{
    private array $hooks = [];

    protected function before(): void
    {
        $this->io->title('husky > uninstalling up git hooks');

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

        $hookDir     = $gitDir . DIRECTORY_SEPARATOR . self::GIT_HOOK_DIRECTORY_NAME;
        $this->hooks = \array_map(fn ($item) => $hookDir . DIRECTORY_SEPARATOR . $item, self::HOOK_LIST);
    }

    protected function execute(): void
    {
        $dryRun = $this->input->getOption('dry-run');

        if (!$dryRun) {
            $this->removeHooks();
        }
    }

    protected function after(): void
    {
        $this->io->success('husky uninstall');
    }

    private function removeHooks(): void
    {
        \array_map(function ($hook) {
            if ($this->canRemove($hook)) {
                $this->removeHook($hook);
            }
        }, $this->hooks);
    }

    protected function removeHook(string $filename): void
    {
        @\unlink($filename);
    }

    protected function canRemove(string $filename): bool
    {
        if ($this->fs->exists($filename)) {
            return Util::ishusky(\file_get_contents($filename));
        }

        return false;
    }
}
