<?php

namespace Tnapf\JsonMapper\Type;

use Attribute;
use Tnapf\JsonMapper\CaseConverterInterface;

#[Attribute(Attribute::TARGET_CLASS)]
class SnakeToCamelCase implements CaseConverterInterface
{
    public function convertToCase(string $string): string
    {
        return lcfirst(str_replace('_', '', ucwords($string, '_')));
    }

    public function convertFromCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }
}
