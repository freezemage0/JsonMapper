<?php

namespace Tnapf\JsonMapper\Type;

use Tnapf\JsonMapper\InvalidArgumentException;

interface ConvertableType extends BaseType
{
    /**
     * Validates input value and optionally casts to a suitable type.
     *
     * In case when input value is an object, implementers MUST preserve the original object state.
     *
     *
     * @throws InvalidArgumentException
     */
    public function convert(mixed $data): mixed;
}
