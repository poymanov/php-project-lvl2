<?php

declare(strict_types=1);

namespace Differ;

const ADD_CHANGE_TYPE = '+';
const REMOVE_TYPE = '-';
const ACTUAL_TYPE = ' ';

/**
 * Получение разницы двух файлов
 *
 * @param string $firstFilePath
 * @param string $secondFilePath
 *
 * @return array
 */
function getDiff(string $firstFilePath, string $secondFilePath): array
{
    $data = [];

    $firstFile = parseFile($firstFilePath);
    $secondFile = parseFile($secondFilePath);

    ksort($firstFile);

    foreach ($firstFile as $key => $value) {
        if (!isset($secondFile[$key])) {
            $data[] = buildRemoveItemForDiff($key, $value);
        } else {
            $firstValue = $value;
            $secondValue = $secondFile[$key];

            if ($firstValue !== $secondValue) {
                $data[] = buildRemoveItemForDiff($key, $value);
                $data[] = buildAddChangeItemForDiff($key, $secondValue);
            } else {
                $data[] = buildActualItemForDiff($key, $value);
            }
        }
    }

    foreach ($secondFile as $key => $value) {
        if (!isset($firstFile[$key])) {
            $data[] = buildAddChangeItemForDiff($key, $value);
        }
    }

    return $data;
}

/**
 * Печать результата сравнений
 *
 * @param array $data
 */
function printDiff(array $data): void
{
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

    echo $result;
}

/**
 * Получение значения элемента
 *
 * @param mixed $value
 *
 * @return string
 */
function getValue($value): string
{
    if (!$value) {
        return 'false';
    } elseif ($value == 1) {
        return 'true';
    } else {
        return (string) $value;
    }
}

/**
 * Формирование элемента данных для данных с разницей
 *
 * @param string $type
 * @param        $key
 * @param        $value
 *
 * @return array
 */
function buildItemForDiff(string $type, $key, $value): array
{
    return [
        'type'  => $type,
        'key'   => $key,
        'value' => getValue($value)
    ];
}

/**
 * Формирование элемента данных для данных с добавлением/изменением
 *
 * @param $key
 * @param $value
 *
 * @return array
 */
function buildAddChangeItemForDiff($key, $value): array
{
    return buildItemForDiff(ADD_CHANGE_TYPE, $key, $value);
}

/**
 * Формирование элемента данных для данных с удалением
 *
 * @param $key
 * @param $value
 *
 * @return array
 */
function buildRemoveItemForDiff($key, $value): array
{
    return buildItemForDiff(REMOVE_TYPE, $key, $value);
}

/**
 * Формирование элемента данных для данных без изменений
 *
 * @param $key
 * @param $value
 *
 * @return array
 */
function buildActualItemForDiff($key, $value): array
{
    return buildItemForDiff(ACTUAL_TYPE, $key, $value);
}
