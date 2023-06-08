<?php

namespace Tnapf\JsonMapper\Type;

use Attribute;
use ReflectionEnum;
use Tnapf\JsonMapper\Compatibility\BaseTypeConvertable;
use Tnapf\JsonMapper\InvalidArgumentException;
use Tnapf\JsonMapper\MapperException;
use Tnapf\JsonMapper\MapperInterface;
use UnitEnum;

#[Attribute(Attribute::TARGET_PROPERTY)]
class EnumerationType implements ConvertableType
{
    use BaseTypeConvertable;

    private ReflectionEnum $reflector;
    private MapperInterface $mapper;

    /**
     * @throws InvalidArgumentException
     * @throws MapperException
     */
    public function __construct(
        public readonly string $name,
        public readonly string $enum,
        public readonly bool $caseSensitive = true,
        public readonly bool $nullable = false
    ) {
        if (!enum_exists($this->enum)) {
            throw InvalidArgumentException::createTypeDoesNotExist(sprintf('Enum %s', $this->enum));
        }

        $this->reflector = new ReflectionEnum($this->enum);
        if (!$this->reflector->isBacked()) {
            throw new MapperException('Non-backed enums cannot be mapped');
        }
    }

    /**
     * @throws InvalidArgumentException
     * @throws MapperException
     */
    public function convert(mixed $data): UnitEnum
    {
        if (!is_string($data) && !is_int($data)) {
            throw InvalidArgumentException::createInvalidType('int or string', gettype($data));
        }

        $comparator = $this->caseSensitive ? strcmp(...) : strcasecmp(...);

        foreach ($this->reflector->getCases() as $case) {
            if ($comparator($case->getBackingValue(), $data) === 0) {
                return $case->getValue();
            }
        }

        throw new InvalidArgumentException(sprintf('%s is not an enum case.', $data));
    }

    public function setMapper(MapperInterface $mapper): void
    {
        $this->mapper = $mapper;
    }
}
