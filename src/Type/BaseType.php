<?php

namespace Tnapf\JsonMapper\Type;

/**
 * @deprecated Classes that rely on this interface should rely on {@link ConvertableType} instead.
 *
 * TODO: Remove in the next major release.
 */
interface BaseType
{
    public function isType(mixed $data): bool;
}
