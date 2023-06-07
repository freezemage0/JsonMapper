<?php

namespace Tnapf\JsonMapper\Type;

use Attribute;
use Tnapf\JsonMapper\Compatibility\BaseTypeConvertable;
use Tnapf\JsonMapper\InvalidArgumentException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class StringType implements ConvertableType
{
    use BaseTypeConvertable;

    public function __construct(
        public readonly string $name,
        public readonly bool $nullable = false,
    ) {
    }

    public function convert(mixed $data): string
    {
        if (!is_scalar($data) && !($data instanceof \Stringable) && !method_exists($data, '__toString')) {
            throw InvalidArgumentException::createInvalidType('scalar or stringable', gettype($data));
        }

        return (string) $data;
    }
}
