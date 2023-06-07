<?php

namespace Tnapf\JsonMapper\Type;

use Attribute;
use Tnapf\JsonMapper\Compatibility\BaseTypeConvertable;
use Tnapf\JsonMapper\InvalidArgumentException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ScalarArray implements ConvertableType
{
    use BaseTypeConvertable;

    public function __construct(
        public readonly string $name,
        public readonly PrimitiveType $type,
        public readonly bool $nullable = false,
    ) {
    }

    public function isType(mixed $data): bool
    {
        $method = "is_{$this->type->value}";

        foreach ($data as $value) {
            if ($method($value) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function convert(mixed $data): array
    {
        if (!is_array($data)) {
            throw InvalidArgumentException::createInvalidType('array', gettype($data));
        }

        $result = [];
        foreach ($data as $item) {
            if (!is_scalar($item)) {
                throw InvalidArgumentException::createInvalidType('scalar', gettype($item));
            }

            settype($item, $this->type->value);
            $result[] = $item;
        }

        return $result;
    }
}
