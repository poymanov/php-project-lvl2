<?php

declare(strict_types=1);

namespace Differ;

function getDiff(string $firstFilePath, string $secondFilePath): string
{
    $data = [];

    $firstFile = json_decode(file_get_contents($firstFilePath), true);
    $secondFile = json_decode(file_get_contents($secondFilePath), true);

    ksort($firstFile);

    foreach ($firstFile as $key => $value) {
        if (!isset($secondFile[$key])) {
            $data[] = [
                'type'  => '-',
                'key'   => $key,
                'value' => getValue($value)
            ];
        } else {
            $firstValue = getValue($value);
            $secondValue = getValue($secondFile[$key]);

            if ($firstValue !== $secondValue) {
                $data[] = [
                    'type' => '-',
                    'key' => $key,
                    'value' => $firstValue
                ];

                $data[] = [
                    'type' => '+',
                    'key' => $key,
                    'value' => $secondValue
                ];
            } else {
                $data[] = [
                    'type' => '',
                    'key' => $key,
                    'value' => $firstValue
                ];
            }
        }
    }

    foreach ($secondFile as $key => $value) {
        if (!isset($firstFile[$key])) {
            $data[] = [
                'type' => '+',
                'key' => $key,
                'value' => getValue($value)
            ];
        }
    }

    $result = '{' . PHP_EOL;

    foreach ($data as $item) {
        $result .= "  ";

        if ($item['type']) {
            $result .= $item['type'];
        } else {
            $result .= ' ';
        }

        $result .= ' ' . $item['key'] . ': ' . $item['value'] . PHP_EOL;
    }

    $result .= '}' . PHP_EOL;

    return $result;
}

function getValue($value)
{
    if (!$value) {
        return 'false';
    } elseif ($value == 1) {
        return 'true';
    } else {
        return $value;
    }
}
