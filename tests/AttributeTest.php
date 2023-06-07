<?php

namespace Tnapf\JsonMapper\Tests;

use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;
use Tnapf\JsonMapper\MapperException;
use Tnapf\JsonMapper\MapperInterface;
use Tnapf\JsonMapper\Type\AnyArray;
use Tnapf\JsonMapper\Type\Any;
use Tnapf\JsonMapper\Type\Boolean;
use Tnapf\JsonMapper\Type\EnumerationType;
use Tnapf\JsonMapper\Type\ObjectArray;
use Tnapf\JsonMapper\Type\ObjectType;
use Tnapf\JsonMapper\Type\FloatType;
use Tnapf\JsonMapper\Type\PrimitiveType;
use Tnapf\JsonMapper\Type\IntType;
use Tnapf\JsonMapper\Type\ScalarArray;
use Tnapf\JsonMapper\Type\StringType;
use Tnapf\JsonMapper\Tests\Fakes\IssueCategory;
use Tnapf\JsonMapper\Tests\Fakes\IssueState;
use Tnapf\JsonMapper\Tests\Fakes\RolePermission;
use Tnapf\JsonMapper\Tests\Fakes\AttributeDuplication;

class AttributeTest extends TestCase
{
    public function testAnyArray()
    {
        $anyArray = new AnyArray(name: 'anyArray');

        $this->assertTrue($anyArray->isType(['test', 1, 1.52]));
        $this->assertFalse($anyArray->isType('test'));
    }

    public function testAnyType()
    {
        $anyType = new Any(name: 'anyType');

        $this->assertTrue($anyType->isType('test'));
        $this->assertTrue($anyType->isType(1));
    }

    public function testBoolType()
    {
        $boolType = new Boolean(name: 'boolType');

        $this->assertTrue($boolType->isType(true));
        $this->assertTrue($boolType->isType(false));
        $this->assertFalse($boolType->isType('test'));
    }

    public function testFloatType()
    {
        $floatType = new FloatType(name: 'floatType');

        $this->assertTrue($floatType->isType(1.52));
        $this->assertFalse($floatType->isType('test'));
    }

    public function testIntType()
    {
        $intType = new IntType(name: 'intType');

        $this->assertTrue($intType->isType(1));
        $this->assertFalse($intType->isType('test'));
    }

    public function testObjectArray()
    {
        $mapperMock = $this->createMock(MapperInterface::class);
        $mapperMock
                ->expects($this->exactly(3))
                ->method('map')
                ->with(
                        new IsEqual(stdClass::class),
                        new IsType(IsType::TYPE_ARRAY)
                )
                ->willReturn(new stdClass());

        $objectArray = new ObjectArray(name: 'objectArray', class: stdClass::class);
        $objectArray->setMapper($mapperMock);

        $this->assertTrue($objectArray->isType([[], []]));
        $this->assertFalse($objectArray->isType(['test']));
        $this->assertFalse($objectArray->isType('test'));
        $this->assertFalse($objectArray->isType([[], 'test']));
    }

    public function testObjectType()
    {
        $mapperMock = $this->createMock(MapperInterface::class);
        $mapperMock
                ->expects($this->once())
                ->method('map')
                ->with(
                        new IsEqual(stdClass::class),
                        new IsType(IsType::TYPE_ARRAY)
                )
                ->willReturn(new stdClass());

        $objectType = new ObjectType(name: 'objectType', class: stdClass::class);
        $objectType->setMapper($mapperMock);

        $this->assertTrue($objectType->isType([]));
        $this->assertFalse($objectType->isType('test'));
    }

    public function testStringType()
    {
        $stringType = new StringType(name: 'stringType');

        $this->assertTrue($stringType->isType('test'));
        $this->assertFalse($stringType->isType(1));
    }

    public function testPrimitiveArray()
    {
        foreach (PrimitiveType::cases() as $primitive) {
            $primitiveType = new ScalarArray(name: 'primitiveType', type: $primitive);

            $trueType = match ($primitive) {
                PrimitiveType::STRING => ['test', 'test2'],
                PrimitiveType::INT => [1, 2],
                PrimitiveType::FLOAT => [1.52, 2.52],
                PrimitiveType::OBJECT => [(object)['test' => 'test'], (object)['test2' => 'test2']],
                PrimitiveType::BOOL => [true, false],
            };

            $falseType = match ($primitive) {
                PrimitiveType::STRING => ['test', 1],
                PrimitiveType::INT => [1, 'test'],
                PrimitiveType::FLOAT => [1.52, 'test'],
                PrimitiveType::OBJECT => [new stdClass(), ['test2' => 2]],
                PrimitiveType::BOOL => [true, 'test'],
            };

            $this->assertTrue($primitiveType->isType($trueType));
            $this->assertFalse($primitiveType->isType($falseType));
        }
    }

    public function testPureEnumerationTypeCannotBeMapped(): void
    {
        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('Non-backed enums cannot be mapped');

        new EnumerationType('issueState', IssueState::class);
    }

    public function testIntBackedEnumerationType(): void
    {
        $type = new EnumerationType('permission', RolePermission::class);

        $this->assertTrue($type->isType(1));
        $this->assertFalse($type->isType(5));
    }

    public function testStringBackedEnumerationType(): void
    {
        $type = new EnumerationType('issueCategory', IssueCategory::class);

        $this->assertTrue($type->isType('general'));
        $this->assertFalse($type->isType('INVALID'));
    }

    public function testStringBackedEnumerationTypeCaseSensitive(): void
    {
        $type = new EnumerationType('issueCategory', IssueCategory::class, true);

        $this->assertFalse($type->isType('GENERAL'));
        $this->assertFalse($type->isType('Bug'));
        $this->assertFalse($type->isType('test123'));
        $this->assertTrue($type->isType('enhancement'));
    }

    public function testEnumerationTypeInvalidData(): void
    {
        $type = new EnumerationType('permission', RolePermission::class);

        $this->assertFalse($type->isType(new stdClass()));
    }
}
