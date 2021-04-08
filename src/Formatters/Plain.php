<?php

namespace Differ\Differ\Formatters\Plain;

use function Funct\Collection\flattenAll;

/**
 * @param mixed $value
 * @return string
 */
function stringify($value)
{
    $typeFormats = [
        'string' => fn($value) => "'{$value}'",
        'integer' => fn($value) => (string) $value,
        'object' => fn($value) => '[complex value]',
        'array' => fn($value) => '[complex value]',
        'boolean' => fn($value) => $value ? 'true' : 'false',
        'NULL' => fn($value) => 'null'
    ];

    $type = gettype($value);

    return $typeFormats[$type]($value);
}

function generatePlainOutput(array $tree, array $propertyNames): array
{
    $output = array_map(function ($child) use ($propertyNames) {
        $name = implode('.', [...$propertyNames, $child['name']]);

        switch ($child['state']) {
            case 'added':
                $value = stringify($child['value']);
                return "Property '{$name}' was added with value: {$value}";

            case 'removed':
                return "Property '{$name}' was removed";

            case 'unchanged':
                return "";

            case 'changed':
                $oldValue = stringify($child['oldValue']);
                $newValue = stringify($child['newValue']);
                return "Property '{$name}' was updated. From {$oldValue} to {$newValue}";

            case 'nested':
                return generatePlainOutput($child['children'], [...$propertyNames, $child['name']]);

            default:
                throw new \Exception("Invalid node state: {$child['state']}");
        }
    }, $tree);

    $filteredOutput = array_filter($output, fn($part) => $part !== '');

    return flattenAll($filteredOutput);
}

function render(array $data): string
{
    return implode("\n", generatePlainOutput($data, []));
}
