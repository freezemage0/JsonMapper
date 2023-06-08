<?php

namespace Tnapf\JsonMapper;

interface CaseConverterInterface
{
    public function convertToCase(string $string): string;

    public function convertFromCase(string $string): string;
}
