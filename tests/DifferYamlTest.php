<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use function Differ\getDiff;

class DifferYamlTest extends TestCase
{
    public function testSimpleStructure(): void
    {
        $firstFilePath  = 'tests/fixtures/simple/yml/file1.yml';
        $secondFilePath = 'tests/fixtures/simple/yml/file2.yml';

        $expected = [
            ['type' => '-', 'key' => 'follow', 'value' => 'false'],
            ['type' => ' ', 'key' => 'host', 'value' => 'hexlet.io'],
            ['type' => '-', 'key' => 'proxy', 'value' => '123.234.53.22'],
            ['type' => '-', 'key' => 'timeout', 'value' => '50'],
            ['type' => '+', 'key' => 'timeout', 'value' => '20'],
            ['type' => '+', 'key' => 'verbose', 'value' => 'true'],
        ];

        $this->assertEquals($expected, getDiff($firstFilePath, $secondFilePath));
    }
}
