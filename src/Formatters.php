<?php

namespace Differ\Differ\Formatters;

use function Differ\Differ\Formatters\Stylish\render as renderInStylish;
use function Differ\Differ\Formatters\Plain\render as renderInPlain;
use function Differ\Differ\Formatters\Json\render as renderInJson;

function formatData(array $data, string $format): string
{
    $formatters = [
        'stylish' => fn($data) => renderInStylish($data),
        'plain' => fn($data) => renderInPlain($data),
        'json' => fn($data) => renderInJson($data)
    ];

    if (!array_key_exists($format, $formatters)) {
        throw new \Exception("Unsupported format: {$format}");
    }

    return $formatters[$format]($data);
}
