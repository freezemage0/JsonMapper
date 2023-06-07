<?php

namespace Tnapf\JsonMapper\Type;

use Attribute;
use Tnapf\JsonMapper\Compatibility\BaseTypeConvertable;
use Tnapf\JsonMapper\InvalidArgumentException;
use Tnapf\JsonMapper\MapperAwareInterface;
use Tnapf\JsonMapper\MapperInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class UnionType implements ConvertableType, MapperAwareInterface
{
    use BaseTypeConvertable;

    /** @var array<array-key, BaseType> */
    public readonly array   $types;

    private MapperInterface $mapper;

    public function __construct(public readonly string $name, public bool $nullable = false, ConvertableType ...$types)
    {
        $this->types = $types;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function convert(mixed $data): mixed
    {
        $invalidTypes = [];

        foreach ($this->types as $type) {
            try {
                $type = clone $type;
                if ($type instanceof MapperAwareInterface) {
                    $type->setMapper($this->mapper);
                }

                return $type->convert($data);
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

    public function setMapper(MapperInterface $mapper): void
    {
        $this->mapper = $mapper;
    }
}
