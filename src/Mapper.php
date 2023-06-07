<?php

namespace Tnapf\JsonMapper;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionUnionType;
use Tnapf\JsonMapper\CaseConverter\NullConverter;
use Tnapf\JsonMapper\Type\Any;
use Tnapf\JsonMapper\Type\AnyArray;
use Tnapf\JsonMapper\Type\BaseType;
use Tnapf\JsonMapper\Type\Boolean;
use Tnapf\JsonMapper\Type\ConvertableType;
use Tnapf\JsonMapper\Type\FloatType;
use Tnapf\JsonMapper\Type\IntType;
use Tnapf\JsonMapper\Type\ObjectType;
use Tnapf\JsonMapper\Type\StringType;
use Tnapf\JsonMapper\Type\UnionType;

class Mapper implements MapperInterface
{
    protected CaseConverterInterface $caseConversion;
    protected object          $instance;
    protected ReflectionClass $reflector;

    /**
     * @var array<array-key, BaseType>
     */
    protected array $types;

    /**
     * @var array<class-string, BaseType[]>
     */
    protected static array $attributesCache = [];

    protected array $data;

    public function map(string $class, array $data): object
    {
        $mapper = new self();
        $mapper->data = $data;
        $mapper->reflector = new ReflectionClass($class);

        if ($attributes = $mapper->reflector->getAttributes(CaseConverterInterface::class, ReflectionAttribute::IS_INSTANCEOF)) {
            if (count($attributes) > 1) {
                throw new MapperException("{$class} has more than one case conversion attribute");
            }

            $mapper->caseConversion = $attributes[0]->newInstance();
        } else {
            $mapper->caseConversion = new NullConverter();
        }

        $mapper->types = self::$attributesCache[$class] ?? [];
        $mapper->instance = $mapper->reflector->newInstanceWithoutConstructor();

        $object = $mapper->doMapping();

        if (!isset(self::$attributesCache[$class])) {
            self::$attributesCache[$class] = $mapper->types;
        }

        return $object;
    }

    protected function convertNameToCase(string $name): string
    {
        return $this?->caseConversion->convertToCase($name) ?? $name;
    }

    protected function convertNameFromCase(string $name): string
    {
        return $this?->caseConversion->convertFromCase($name) ?? $name;
    }

    /**
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws MapperException
     */
    protected function doMapping(): object
    {
        $types = $this->getTypes();

        foreach ($types as $type) {
            $data = $this->data[$this->convertNameFromCase($type->name)] ?? null;

            if ($data === null) {
                if ($type->nullable) {
                    continue;
                }

                throw new InvalidArgumentException("Property {$type->name} on {$this->reflector->name} not nullable");
            }

            $data = $type->convert($data);

            $camelCasePropertyName = $this->convertNameToCase($type->name);
            $property = $this->reflector->getProperty($camelCasePropertyName);
            $property->setValue($this->instance, $data);
        }

        return $this->instance;
    }

    protected function getTypes(): array
    {
        if (!empty($this->types)) {
            return $this->types;
        }

        $properties = $this->reflector->getProperties();

        foreach ($properties as $property) {
            $attributes = array_map(
                static fn (ReflectionAttribute $attribute) => $attribute->newInstance(),
                $property->getAttributes(BaseType::class, ReflectionAttribute::IS_INSTANCEOF)
            );

            $name = $property->getName();
            $type = $property->getType();

            $this->types[$name] = [];
            if (!empty($attributes)) {
                $this->types[$name] = [...$this->types[$name], ...$attributes];

                continue;
            }

            if ($type === null) {
                $this->types[$name] = new Any($name);

                continue;
            }

            if ($type instanceof ReflectionUnionType) {
                $subTypes = array_map(
                    static fn (ReflectionNamedType $type): ConvertableType => $this->inferScalarType($name, $type),
                    $type->getTypes()
                );
                $type = new UnionType($name, $type->allowsNull(), ...$subTypes);
            } else {
                $type = $this->inferScalarType($name, $type);
            }

            $this->types[$name] = $type;
        }

        return $this->types;
    }

    private function inferScalarType(string $propertyName, ReflectionNamedType $type): ConvertableType
    {
        return match ($type->getName()) {
            'int' => new IntType(name: $propertyName, nullable: $type->allowsNull()),
            'bool' => new Boolean(name: $propertyName, nullable: $type->allowsNull()),
            'string' => new StringType(name: $propertyName, nullable: $type->allowsNull()),
            'array' => new AnyArray(name: $propertyName, nullable: $type->allowsNull()),
            'float' => new FloatType(name: $propertyName, nullable: $type->allowsNull()),
            default => new ObjectType(name: $propertyName, class: $type->getName(), nullable: $type->allowsNull()),
        };
    }
}
