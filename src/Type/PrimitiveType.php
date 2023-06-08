<?php

namespace Tnapf\JsonMapper\Type;

enum PrimitiveType: string
{
    case STRING = 'string';
    case INT = 'int';
    case FLOAT = 'float';
    case OBJECT = 'object';
    case BOOL = 'bool';
}
