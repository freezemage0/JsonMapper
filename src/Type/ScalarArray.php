<?php

namespace Tnapf\JsonMapper\Type;

use Attribute;
use Tnapf\JsonMapper\Compatibility\BaseTypeConvertable;
use Tnapf\JsonMapper\InvalidArgumentException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ScalarArray implements ConvertableType
{
    use BaseTypeConvertable;

    private array $types;

    public function __construct(
        public readonly string $name,
        public readonly bool $nullable = false,
        PrimitiveType ...$types,
    ) {
        $this->types = $types;
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
            $invalidTypes = [];
            foreach ($this->types as $type) {
                $validator = "is_{$type->value}";
                if ($validator($item)) {
                    continue 2;
                }
                $invalidTypes[] = $type->value;
            }
            if (!empty($invalidTypes)) {
                throw InvalidArgumentException::createInvalidType(
                    implode(' or ', $invalidTypes),
                    gettype($item)
                );
            }

            $result[] = $item;
        }

        return $result;
    }
}
