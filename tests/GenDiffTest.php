<?php

namespace Php\Package\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    private function makeFilepath(string $filename): string
    {
        $parts = [__DIR__, 'fixtures', $filename];
        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    /**
     * @dataProvider defaultOutputProvider
     */
    public function testDefaultFormatOutput(string $filename1, string $filename2, string $expectedFilename): void
    {
        $expectedOutput = file_get_contents($this->makeFilepath($expectedFilename));
        $this->assertSame($expectedOutput, genDiff($this->makeFilepath($filename1), $this->makeFilepath($filename2)));
    }

    /**
     * @dataProvider differentFormatsProvider
     */
    public function testDifferentFormatOutputs(
        string $filename1,
        string $filename2,
        string $format,
        string $expectedFilename
    ): void {
        $expectedOutput = file_get_contents($this->makeFilepath($expectedFilename));
        $this->assertSame($expectedOutput, genDiff(
            $this->makeFilepath($filename1),
            $this->makeFilepath($filename2),
            $format
        ));
    }

    public function defaultOutputProvider(): array
    {
        return [
            'default output for json files' => [
                'before.json',
                'after.json',
                'expectedStylish.txt'
            ],
            'default output for yaml files' => [
                'before.yaml',
                'after.yaml',
                'expectedStylish.txt'
            ]
        ];
    }

    public function differentFormatsProvider(): array
    {
        return [
            'stylish output' => [
                'before.json',
                'after.json',
                'stylish',
                'expectedStylish.txt'
            ],
            'plain output' => [
                'before.json',
                'after.json',
                'plain',
                'expectedPlain.txt'
            ],
            'json output' => [
                'before.json',
                'after.json',
                'json',
                'expectedJson.txt'
            ]
        ];
    }
}
