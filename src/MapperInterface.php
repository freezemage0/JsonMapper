<?php

namespace Tnapf\JsonMapper;

use ReflectionException;

interface MapperInterface
{
    /**
     * @template T
     *
     * @param class-string<T> $class
     *
     * @throws MapperException
     * @throws ReflectionException
     *
     * @return T
     */
    public function map(string $class, array $data): object;
}
