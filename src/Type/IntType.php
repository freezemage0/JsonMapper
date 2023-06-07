<?php

namespace Tnapf\JsonMapper\Type;

use Attribute;
use Tnapf\JsonMapper\Compatibility\BaseTypeConvertable;
use Tnapf\JsonMapper\InvalidArgumentException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class IntType implements ConvertableType
{
    use BaseTypeConvertable;

    public function __construct(
        public readonly string $name,
        public readonly bool $nullable = false,
    ) {
    }

    public function convert(mixed $data): int
    {
        if (!is_int($data)) {
            throw InvalidArgumentException::createInvalidType('scalar', gettype($data));
        }

        return (int) $data;
    }
}
