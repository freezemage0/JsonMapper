<?php

namespace Tnapf\JsonMapper\Type;

use Attribute;
use Tnapf\JsonMapper\InvalidArgumentException;
use Tnapf\JsonMapper\MapperAwareInterface;
use Tnapf\JsonMapper\MapperException;
use Tnapf\JsonMapper\MapperInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ObjectArray implements ConvertableType, MapperAwareInterface
{
    private MapperInterface $mapper;

    public function __construct(
        public readonly string $name,
        public readonly string $class,
        public readonly bool $nullable = false
    ) {
    }

    public function isType(mixed $data): bool
    {
        if (!is_array($data)) {
            return false;
        }

        foreach ($data as $value) {
            if (!$value instanceof $this->class) {
                return false;
            }
        }

        return true;
    }

    public function convert(mixed $data): array
    {
        if (!is_array($data)) {
            throw InvalidArgumentException::createInvalidType('array', $data);
        }

        $result = [];
        foreach ($data as $item) {
            try {
                $result[] = $this->mapper->map($this->class, $item);
            } catch (MapperException $e) {
                throw new InvalidArgumentException("Could not convert data item to {$this->class}");
            }
        }

        return $result;
    }

    public function setMapper(MapperInterface $mapper): void
    {
        $this->mapper = $mapper;
    }
}
