<?php

namespace Tnapf\JsonMapper\Compatibility;

use Tnapf\JsonMapper\MapperException;
use Tnapf\JsonMapper\Type\BaseType;
use Tnapf\JsonMapper\Type\ConvertableType;

/**
 * Default implementation of {@link BaseType::isType()} for {@link ConvertableType} implementors.
 *
 * @mixin ConvertableType
 */
trait BaseTypeConvertable
{
    public function isType(mixed $data): bool
    {
        try {
            $this->convert($data);

            return true;
        } catch (MapperException) {
            return false;
        }
    }
}
