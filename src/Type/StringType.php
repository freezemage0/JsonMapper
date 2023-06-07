<?php

namespace Tnapf\JsonMapper\Type;

use Attribute;
use Stringable;
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
        if (is_object($data) && method_exists($data, '__toString') || $data instanceof Stringable) {
            $data = (string) $data;
        }

        if (!is_string($data)) {
            throw InvalidArgumentException::createInvalidType('string', gettype($data));
        }

        return $data;
    }
}
