<?php

namespace Tnapf\JsonMapper;

class InvalidArgumentException extends MapperException
{
    public static function createInvalidType(string $expected, string $actual): InvalidArgumentException
    {
        return new InvalidArgumentException(sprintf('Expected type %s, got %s', $expected, $actual));
    }

    public static function createTypeDoesNotExist(string $name): InvalidArgumentException
    {
        return new InvalidArgumentException(sprintf('%s type does not exist', $name));
    }
}
