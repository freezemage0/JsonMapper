<?php

namespace Tnapf\JsonMapper\Attributes;

interface BaseType extends ConvertableType
{
    public function isType(mixed $data): bool;
}
