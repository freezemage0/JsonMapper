<?php

namespace Tnapf\JsonMapper\Tests;

use PHPUnit\Framework\TestCase;
use Tnapf\JsonMapper\CaseConverter\SnakeToCamelConverter;

class CaseConversionTest extends TestCase
{
    public function testSnakeToCamelCase()
    {
        $converter = new SnakeToCamelConverter();
        $snakeCase = 'snake_case';
        $camelCase = 'snakeCase';

        $this->assertSame($snakeCase, $converter->convertFromCase($snakeCase), 'Snake case should not be converted');
        $this->assertSame($camelCase, $converter->convertToCase($camelCase), 'Camel case should not be converted');
        $this->assertSame($snakeCase, $converter->convertFromCase($snakeCase), 'Snake case should not be changed');
        $this->assertSame($camelCase, $converter->convertToCase($camelCase), 'Camel case should not be changed');
    }
}
