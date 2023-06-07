<?php

namespace Tnapf\JsonMapper\Type;

use Attribute;
use Tnapf\JsonMapper\Compatibility\BaseTypeConvertable;
use Tnapf\JsonMapper\InvalidArgumentException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class AnyArray implements ConvertableType
{
    use BaseTypeConvertable;

    public function __construct(
        public readonly string $name,
        public readonly bool $nullable = false
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function convert(mixed $data): array
    {
        if (!is_array($data)) {
            throw InvalidArgumentException::createInvalidType('array', gettype($data));
        }

        return $data;
    }
}
