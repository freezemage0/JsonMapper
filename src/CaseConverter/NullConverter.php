<?php

namespace Tnapf\JsonMapper\CaseConverter;

use Tnapf\JsonMapper\CaseConverterInterface;

final class NullConverter implements CaseConverterInterface
{
    public function convertToCase(string $string): string
    {
        return $string;
    }

    public function convertFromCase(string $string): string
    {
        return $string;
    }
}
