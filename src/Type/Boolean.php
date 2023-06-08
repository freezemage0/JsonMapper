<?php

namespace Tnapf\JsonMapper\Type;

use Attribute;
use Tnapf\JsonMapper\Compatibility\BaseTypeConvertable;
use Tnapf\JsonMapper\InvalidArgumentException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Boolean implements ConvertableType
{
    use BaseTypeConvertable;

    public function __construct(
        public readonly string $name,
        public readonly bool $nullable = false,
    ) {
    }

    public function convert(mixed $data): bool
    {
        if (!is_bool($data)) {
            throw InvalidArgumentException::createInvalidType('boolean', gettype($data));
        }

        return $data;
    }
}
