<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;
use Tnapf\JsonMapper\InvalidArgumentException;
use Tnapf\JsonMapper\MapperInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class UnionType implements BaseType, ConvertableType
{
    /** @var array<array-key, BaseType> */
    public readonly array $types;

    public function __construct(public readonly string $name, public bool $nullable = false, BaseType ...$types)
    {
        $this->types = $types;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function cast(MapperInterface $mapper, mixed $data): mixed
    {
        $invalidTypes = [];

        foreach ($this->types as $type) {
            try {
                return $type->cast($mapper, $data);
            } catch (InvalidArgumentException) {
                $invalidTypes[] = get_class($type);

                continue;
            }
        }

        throw new InvalidArgumentException(sprintf(
            'Value %s is not of types %s',
            $data,
            implode(', ', $invalidTypes)
        ));
    }

    public function isType(mixed $data): bool
    {
        // noop
        return false;
    }
}
