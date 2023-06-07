<?php

namespace Tnapf\JsonMapper\Type;

use Attribute;
use Tnapf\JsonMapper\Compatibility\BaseTypeConvertable;
use Tnapf\JsonMapper\InvalidArgumentException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FloatType implements ConvertableType
{
    use BaseTypeConvertable;

    public function __construct(
        public readonly string $name,
        public readonly bool $nullable = false,
    ) {
    }

    public function convert(mixed $data): float
    {
        if (!is_numeric($data)) {
            throw InvalidArgumentException::createInvalidType('int or float', gettype($data));
        }

        return (float) $data;
    }
}
