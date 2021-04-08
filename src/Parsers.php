<?php

namespace Differ\Differ\Parsers;

use function Differ\Differ\Parsers\parseJson;
use function Differ\Differ\Parsers\parseYaml;

function parseData(string $data, string $parserType): object
{
    $parsers = [
        'json' => fn($data) => parseJson($data),
        'yaml' => fn($data) => parseYaml($data),
        'yml' => fn($data) => parseYaml($data)
    ];

    if (!array_key_exists($parserType, $parsers)) {
        throw new \Exception("Unsupported parser type: {$parserType}");
    }

    return $parsers[$parserType]($data);
}
