<?php

namespace Tnapf\JsonMapper\Attributes;

use Tnapf\JsonMapper\MapperInterface;

interface ConvertableType extends BaseType
{
    public function cast(MapperInterface $mapper, mixed $data): mixed;
}
