<?php

use CloudCastle\Core\Validator\Types\BoolType;
use CloudCastle\Core\Validator\Types\DateTimeType;
use CloudCastle\Core\Validator\Types\DateType;
use CloudCastle\Core\Validator\Types\EnumType;
use CloudCastle\Core\Validator\Types\FloatType;
use CloudCastle\Core\Validator\Types\IntegerType;
use CloudCastle\Core\Validator\Types\JsonType;
use CloudCastle\Core\Validator\Types\NullType;
use CloudCastle\Core\Validator\Types\ObjectType;
use CloudCastle\Core\Validator\Types\StringType;
use CloudCastle\Core\Validator\Types\TimeType;

return [
    'string' => StringType::class,
    'nullable' => NullType::class,
    'bool' => BoolType::class,
    'boolean' => BoolType::class,
    'json' => JsonType::class,
    'datetime' => DateTimeType::class,
    'date' => DateType::class,
    'time' => TimeType::class,
    'integer' => IntegerType::class,
    'int' => IntegerType::class,
    'float' => FloatType::class,
    'double' => FloatType::class,
    'enum' => EnumType::class,
    'object' => ObjectType::class,
];