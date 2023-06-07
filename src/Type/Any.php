<?php

namespace Tnapf\JsonMapper\Type;

use Attribute;
use Tnapf\JsonMapper\Compatibility\BaseTypeConvertable;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Any implements ConvertableType
{
    use BaseTypeConvertable;

    public function __construct(
        public readonly string $name,
        public readonly bool $nullable = false
    ) {
    }

    public function convert(mixed $data): mixed
    {
        return $data;
    }
}
