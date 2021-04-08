<?php

namespace Differ\Differ;

use function Differ\Differ\Parsers\parseData;
use function Differ\Differ\Formatters\formatData;
use function Funct\Collection\union;
use function Funct\Collection\sortBy;

function genDiff(string $firstFilepath, string $secondFilepath, string $format = 'stylish'): string
{
    $firstFileContent = read($firstFilepath);
    $secondFileContent = read($secondFilepath);

    $firstParserType = pathinfo($firstFilepath, PATHINFO_EXTENSION);
    $secondParserType = pathinfo($secondFilepath, PATHINFO_EXTENSION);

    $firstFileData = parseData($firstFileContent, $firstParserType);
    $secondFileData = parseData($secondFileContent, $secondParserType);

    $tree = generateDiffTree($firstFileData, $secondFileData);
    $diff = formatData($tree, $format);

    return $diff;
}

function read(string $filepath): string
{
    $absoluteFilepath = (string) realpath($filepath);
    if (!file_exists($absoluteFilepath)) {
        throw new \Exception("File {$filepath} does not exist");
    }

    return (string) file_get_contents($absoluteFilepath);
}

function generateDiffTree(object $dataBefore, object $dataAfter): array
{
    $unitedKeys = union(array_keys(get_object_vars($dataBefore)), array_keys(get_object_vars($dataAfter)));

    $sortedKeys = sortBy($unitedKeys, fn($key) => $key);

    return array_map(function ($key) use ($dataBefore, $dataAfter): array {
        if (!property_exists($dataBefore, $key)) {
            return makeNode($key, 'added', $dataAfter->$key);
        }

        if (!property_exists($dataAfter, $key)) {
            return makeNode($key, 'removed', $dataBefore->$key);
        }

        if (is_object($dataBefore->$key) && is_object($dataAfter->$key)) {
            return makeNode($key, 'nested', null, null, generateDiffTree($dataBefore->$key, $dataAfter->$key));
        }

        if ($dataBefore->$key === $dataAfter->$key) {
            return makeNode($key, 'unchanged', $dataBefore->$key);
        }

        return makeNode($key, 'changed', $dataAfter->$key, $dataBefore->$key);
    }, array_values($sortedKeys));
}

/**
 * @param string $name
 * @param string $state
 * @param object|string|array $newValue
 * @param object|string|array $oldValue
 * @param array $children
 * @return array
 */
function makeNode($name, $state, $newValue = null, $oldValue = null, $children = null)
{
    $complexStates = [
        'changed' => fn($name, $state, $newValue, $oldValue, $children) => [
            'name' => $name,
            'oldValue' => $oldValue,
            'newValue' => $newValue,
            'state' => $state
        ],
        'nested' => fn($name, $state, $newValue, $oldValue, $children) => [
            'name' => $name,
            'state' => $state,
            'children' => $children
        ]
    ];

    if (array_key_exists($state, $complexStates)) {
        return $complexStates[$state]($name, $state, $newValue, $oldValue, $children);
    }

    return [
        'name' => $name,
        'value' => $newValue,
        'state' => $state
    ];
}
