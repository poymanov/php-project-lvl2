<?php

namespace Differ;

use Symfony\Component\Yaml\Yaml;

/**
 * Получение данных для сравнения из файла
 *
 * @param string $filePath
 *
 * @return array
 */
function parseFile(string $filePath)
{
    $content = file_get_contents($filePath);

    $fileInfo = pathinfo($filePath);

    $extension = $fileInfo['extension'];

    if ($extension === 'json') {
        return parseJson($content);
    } elseif (in_array($extension, ['yaml', 'yml'])) {
        return parseYaml($content);
    }
}

/**
 * Получение данных из json
 *
 * @param string $content
 *
 * @return array
 */
function parseJson(string $content): array
{
    return json_decode($content, true);
}

/**
 * Получение данных из yaml
 *
 * @param string $content
 *
 * @return array
 */
function parseYaml(string $content): array
{
    return (array) Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
}
