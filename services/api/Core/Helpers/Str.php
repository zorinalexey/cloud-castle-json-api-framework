<?php

namespace CloudCastle\Core\Helpers;

final class Str
{
    public static function toSnakeCase (string $string): string
    {
        return trim(strtolower(preg_replace('/[A-Z]/', '_$0', $string)), '_');
    }
    
    public static function toCamelCase (string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }
    
}