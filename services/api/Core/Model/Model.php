<?php

namespace CloudCastle\Core\Model;

use PDO;

abstract class Model
{
    public static function getConnection (): PDO
    {
        return new PDO("mysql:host=localhost;dbname=" . self::table(), "", "");
    }
    
    public static function table (): string
    {
        return '';
    }
}