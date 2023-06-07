<?php

namespace Tnapf\JsonMapper\Type;

use Attribute;
use ReflectionException;
use RuntimeException;
use Tnapf\JsonMapper\Compatibility\BaseTypeConvertable;
use Tnapf\JsonMapper\InvalidArgumentException;
use Tnapf\JsonMapper\MapperAwareInterface;
use Tnapf\JsonMapper\MapperException;
use Tnapf\JsonMapper\MapperInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ObjectArray implements ConvertableType, MapperAwareInterface
{
    use BaseTypeConvertable;

    private MapperInterface $mapper;

    public function __construct(
        public readonly string $name,
        public readonly string $class,
        public readonly bool $nullable = false
    ) {
    }

    public function convert(mixed $data): array
    {
        if (!is_array($data)) {
            throw InvalidArgumentException::createInvalidType('array', $data);
        }

        $result = [];
        foreach ($data as $item) {
            try {
                if (!is_array($item)) {
                    throw InvalidArgumentException::createInvalidType('array', gettype($item));
                }

                $result[] = $this->mapper->map($this->class, $item);
            } catch (MapperException $e) {
                throw new InvalidArgumentException("Could not convert data item to {$this->class}");
            } catch (ReflectionException $e) {
                throw new RuntimeException($e->getMessage(), previous: $e);
            }
        }

        return $result;
    }

    public function setMapper(MapperInterface $mapper): void
    {
        $this->mapper = $mapper;
    }
}
