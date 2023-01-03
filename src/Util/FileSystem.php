<?php

namespace App\Util;

use Symfony\Component\Filesystem\Exception\IOException;

class FileSystem extends \Symfony\Component\Filesystem\Filesystem
{
    public function writeFileSync(string $filename, mixed $content): void
    {
        $dir = \dirname($filename);

        if (!\is_dir($dir)) {
            $this->mkdir($dir);
        }

        if (!\is_writable($dir)) {
            throw new IOException(\sprintf('Unable to write to the "%s" directory .', $dir), 0, null, $dir);
        }

        if (false === @\file_put_contents($filename, $content)) {
            throw new IOException(\sprintf('Failed to write file "%s".', $filename), 0, null, $filename);
        }
    }


    public function readFileSync(string $filename): bool|string
    {
        if (!\is_readable($filename)) {
            throw new IOException(\sprintf('Unable to read to the "%s" file.', $filename), 0, null, $filename);
        }

        return \file_get_contents($filename);
    }
}
