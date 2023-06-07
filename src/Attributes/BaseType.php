<?php

namespace Tnapf\JsonMapper\Attributes;

use Tnapf\JsonMapper\MapperInterface;

interface BaseType
{
    public function cast(MapperInterface $mapper, mixed $data): mixed;

    //public function isType(mixed $data): bool;
}
