<?php

namespace App\Util;

class Util
{
    const PHP5 = 5;

    const PHP7 = 7;

    public static $vendor = 'vendor';

    public static $bin_dir = 'bin';

    /**
     * @param $dir
     *
     * @return array
     */
    public static function getAllFiles($dir)
    {
        $fileArr = [];
        if (is_dir($dir)) {
            if ($dh = @opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != '.' && $file != '..') {
                        $fileArr[] = $file;
                    }
                }
                //关闭
                closedir($dh);
            }
        }

        return $fileArr;
    }

    /**
     * @param      $fileOrDir
     * @param null $dstDir
     * @param bool $clear
     *
     * @return array|string
     */
    public static function getFileOrDirPath($fileOrDir, $dstDir = null, $clear = false)
    {
//        static $arr = [];

        static $dstPath = '';
        $files = [];

        if ($clear) {
            $dstPath = '';
        }

        if (is_dir($fileOrDir)) {
            $handle = @opendir($fileOrDir);
            while ($file = readdir($handle)) {
                if (!in_array($file, ['.', '..']) && strpos($file, '.') !== 0) {
                    $files[] = $file;
                }
            }

            // exclude npm directory
            if (
                !in_array('package.json', $files)
                ||
                (in_array('package.json', $files) && in_array('composer.json', $files))
            ) {
                foreach ($files as $file) {
                    $path = $fileOrDir . "/" . $file;

                    if (!empty($dstDir) && $dstDir === $file) {
                        $dstPath = $path;

                        @closedir($handle);

                        return $dstPath;
                    }
//                    array_push($arr, $path);
                    if (is_dir($path)) {
                        self::getFileOrDirPath($path, $dstDir);
                    }
                }
            }

            @closedir($handle);
        }

        return !empty($dstDir) && !empty($dstPath) ? $dstPath : [];
    }

    /**
     * @return array
     */
    public static function getAllCommands()
    {
        $root = self::getDirName(__DIR__);
        $path = $root . DIRECTORY_SEPARATOR . 'Commands';

        return self::getAllFiles($path);
    }

    /**
     * @param $path
     * @param $extension
     *
     * @return array
     */
    public static function getAllFilesByExtension($path, $extension)
    {
        $files = self::getAllFiles($path);

        $result = [];
        foreach ($files as $file) {
            if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === strtolower($extension)) {
                $result[] = $file;
            }
        }

        return $result;
    }

    /**
     * @param $path
     * @param $pattern
     *
     * @return array
     */
    public static function getAllFilesByPattern($path, $pattern)
    {
        $files = self::getAllFiles($path);

        $result = [];
        foreach ($files as $file) {
            if (preg_match($pattern, $file, $match)) {
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
    public static function getDirName($path, $level = 1)
    {
        if ((int)substr(PHP_VERSION, 0, 1) === static::PHP5) {
            $dirName = $path;
            while ($level > 0) {
                $dirName = dirname($dirName);
                $level--;
            }
        } else if ((int)substr(PHP_VERSION, 0, 1) === static::PHP7) {
            $dirName = dirname($path, $level);
        } else {
            die('Not support php version');
        }

        return $dirName;
    }

    public static function getPhpDir()
    {
        $path = __DIR__;

        while (substr($path, -strlen(self::$vendor)) !== self::$vendor) {
            if ($path === '/') {
                return false;
            }
            $path = self::getDirName($path);
        }

        return self::getDirName($path, 2);
    }

    public static function getUserDir()
    {
        $path = str_replace('\\', '/', getcwd());

        $path = self::getGitDir($path);

        return $path;
    }

    public static function getGitDir($path)
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
     * @param $path
     *
     * @return bool
     */
    public static function existsGitDir($path)
    {
        $dir = '.git';

        if (file_exists($path . DIRECTORY_SEPARATOR . $dir)) {
            return true;
        }

        return false;
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public static function existsComposerFile($path)
    {
        $file = 'composer.json';

        if (file_exists($path . DIRECTORY_SEPARATOR . $file)) {
            return true;
        }

        return false;
    }

    public static function getScript($runScriptPath)
    {
        $huskyrc = '~/.huskyrc';
        $render = <<<SHELL
#!/bin/sh
# husky-php

# Hook created by Husky

scriptPath="${runScriptPath}"
command='husky:run'
hookName=`basename "\$0"`
gitParams="$*"

if [ -f "\$scriptPath" ]; then
  if [ -f ${huskyrc} ]; then
    . ${huskyrc}
  fi
  php "\${scriptPath}" \${command} \${hookName} "\${gitParams}"
else
  echo "Can't find Husky, skipping \${hookName} hook"
  echo "You can reinstall it using 'composer require husky-php' or delete this hook"
fi

SHELL;

        return $render;
    }

    /**
     * @param $data
     *
     * @return bool
     */
    public static function isHusky($data)
    {
        $previousHuskyIdentifier = '# husky-php';
        if (strpos($data, $previousHuskyIdentifier) !== false) {
            return true;
        }

        return false;
    }
}
