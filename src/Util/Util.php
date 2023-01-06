<?php

namespace App\Util;

class Util
{
    public const PHP8 = 8;

    /**
     * @var string
     */
    public static string $vendor = 'vendor';

    /**
     * @var string
     */
    public static string $binDir = 'bin';

    /**
     * @param $dir
     *
     * @return array
     */
    public static function getAllFiles($dir): array
    {
        $fileArr = [];
        if (\is_dir($dir) && ($dh = @\opendir($dir))) {
            while (($file = \readdir($dh)) !== false) {
                if ($file !== '.' && $file !== '..') {
                    $fileArr[] = $file;
                }
            }
            \closedir($dh);
        }

        return $fileArr;
    }

    /**
     * @param $fileOrDir
     * @param $dstDir
     * @param bool $clear
     *
     * @return array|string
     */
    public static function getFileOrDirPath($fileOrDir, $dstDir = null, bool $clear = false): array|string
    {
        static $dstPath = '';
        $files          = [];

        if ($clear) {
            $dstPath = '';
        }

        if (\is_dir($fileOrDir)) {
            $handle = @\opendir($fileOrDir);
            while ($file = \readdir($handle)) {
                if (!\in_array($file, ['.', '..'], true) && !\str_starts_with($file, '.')) {
                    $files[] = $file;
                }
            }

            // exclude npm directory
            if (
                !\in_array('package.json', $files, true)
                ||
                (\in_array('package.json', $files, true) && \in_array('composer.json', $files, true))
            ) {
                foreach ($files as $file) {
                    $path = $fileOrDir . '/' . $file;

                    if (!empty($dstDir) && $dstDir === $file) {
                        $dstPath = $path;

                        @\closedir($handle);

                        return $dstPath;
                    }

                    if (\is_dir($path)) {
                        self::getFileOrDirPath($path, $dstDir);
                    }
                }
            }

            @\closedir($handle);
        }

        return !empty($dstDir) && !empty($dstPath) ? $dstPath : [];
    }


    /**
     * @return array
     */
    public static function getAllCommands(): array
    {
        $root = self::getDirName(__DIR__);
        $path = $root . DIRECTORY_SEPARATOR . 'Commands';

        return self::getAllFiles($path);
    }

    /**
     * @param $path
     * @param $pattern
     *
     * @return array
     */
    public static function getAllFilesByPattern($path, $pattern): array
    {
        $files = self::getAllFiles($path);

        $result = [];
        foreach ($files as $file) {
            if (\preg_match($pattern, $file, $match)) {
                $result[] = $file;
            }
        }

        return $result;
    }

    /**
     * @param     $path
     * @param int $level
     *
     * @return string
     */
    public static function getDirName($path, int $level = 1): string
    {
        if ((int)\mb_substr(PHP_VERSION, 0, 1) >= static::PHP8) {
            $dirName = \dirname($path, $level);
        } else {
            die('Not support php version');
        }

        return $dirName;
    }

    /**
     * @return false|mixed
     */
    public static function getUserDir(): mixed
    {
        $path = \str_replace('\\', '/', \getcwd());

        return self::getGitDir($path);
    }

    /**
     * @param $path
     *
     * @return false|mixed
     */
    public static function getGitDir($path): mixed
    {
        if (!self::existsGitDir($path)) {
            $path = self::getDirName($path);

            return self::getGitDir($path);
        }

        if ($path === '/') {
            return false;
        }

        return $path;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public static function existsGitDir(string $path): bool
    {
        $dir = '.git';

        return \file_exists($path . DIRECTORY_SEPARATOR . $dir);
    }

    /**
     * @param string $runScriptPath
     *
     * @return string
     */
    public static function getScript(string $runScriptPath): string
    {
        $huskyrc = '~/.huskyrc.json'; //TODO Calculate runtime value

        return <<<SHELL
#!/bin/bash
# husky

# Hook created by husky

scriptPath="${runScriptPath}"
command='husky:run'
hookName=`basename "\$0"`

if [ -f "\$scriptPath" ]; then
  if [ -f ${huskyrc} ]; then
    . ${huskyrc}
  fi
  
  php "\${scriptPath}" \${command} \${hookName}
  
  if [ $? -ne 0 ]; then
      echo -e "\033[31m\${hookName} Operation interrupted\033[0m"
      exit 1
  fi
else
  echo -e "\033[33mCan't find husky, skipping \${hookName} hook\033[0m"
  echo -e "\033[33mYou can reinstall it using 'composer require husky/husky' or delete this hook\033[0m"
fi

SHELL;
    }

    /**
     * @param string $data
     *
     * @return bool
     */
    public static function ishusky(string $data): bool
    {
        $previoushuskyIdentifier = '# husky';

        return \str_contains($data, $previoushuskyIdentifier);
    }
}
