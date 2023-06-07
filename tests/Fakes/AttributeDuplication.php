<?php

namespace Tnapf\JsonMapper\Tests\Fakes;

use Tnapf\JsonMapper\Type\ScalarArray;
use Tnapf\JsonMapper\Type\PrimitiveType;
use Tnapf\JsonMapper\Type\UnionType;

class AttributeDuplication
{
    #[UnionType('property', types: new ScalarArray('property', PrimitiveType::INT))]
    public array $property;
}
