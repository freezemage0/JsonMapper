<?php

namespace Tnapf\JsonMapper;

interface MapperAwareInterface
{
    public function setMapper(MapperInterface $mapper): void;
}
