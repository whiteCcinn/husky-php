<?php

namespace App\Test\Util;

use PHPUnit\Framework\TestCase;
use App\Util\Util;

class UtilTest extends TestCase
{
    public function testGetScript(): void
    {
        $script = Util::getScript('a/very/long/path');

        $this->assertStringContainsString(
            'scriptPath="a/very/long/path"',
            $script
        );

        $this->assertStringContainsString(
            'command=\'husky:run\'',
            $script
        );

        $this->assertStringContainsString(
            'hookName=`basename "$0"`',
            $script
        );
    }
}
