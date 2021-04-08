<?php

namespace Differ\Differ\Formatters\Json;

function render(array $data): string
{
    return (string) json_encode($data, JSON_PRETTY_PRINT);
}
