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
/**
 * @template T
 */
class ObjectType implements ConvertableType, MapperAwareInterface
{
    use BaseTypeConvertable;

    private MapperInterface $mapper;

    /**
     * @param class-string<T> $class
     */
    public function __construct(
        public readonly string $name,
        public readonly string $class,
        public readonly bool $nullable = false,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     *
     * @return T
     */
    public function convert(mixed $data): object
    {
        try {
            if (!is_array($data)) {
                throw InvalidArgumentException::createInvalidType('array', gettype($data));
            }

            return $this->mapper->map($this->class, $data);
        } catch (ReflectionException $e) {
            // Can't do anything about it or add more context. Rethrow into runtime.
            throw new RuntimeException($e->getMessage(), previous: $e);
        } catch (MapperException $e) {
            throw new InvalidArgumentException(
                "Could not convert data to object of {$this->class}",
                previous: $e
            );
        }
    }

    public function setMapper(MapperInterface $mapper): void
    {
        $this->mapper = $mapper;
    }
}
