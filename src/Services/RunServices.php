<?php

namespace App\Services;

class RunServices extends BaseServices
{
    protected function before()
    {
        $phpDir = $this->phpDir;

        // Get conf from .huskyrc or .husky.json

        foreach (self::CONFIG_FILE_NAME as $configFileName) {
            $confFile = $phpDir . DIRECTORY_SEPARATOR . $configFileName;

            if (!$this->fs->exists($confFile)) {
                continue;
            }

            $userConf = json_decode($this->fs->readFileSync($confFile), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $this->conf = array_merge($this->conf, $userConf);
            }
        }

    }

    protected function execute()
    {
        $invokeHookName = $this->input->getArgument('hookName');

        // TODO: not used
        $gitParams = $this->input->getArgument('gitParams');

        if (isset($this->conf['hooks'])) {
            foreach ($this->conf['hooks'] as $configHookName => $shell) {
                if ($configHookName === $invokeHookName) {
                    $this->output->writeln("<info>husky > {$invokeHookName} </info>");
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        $shell = "bash ${shell}";
                    }
                    system($shell);
                }
            }
        }

        return true;
    }
}