<?php

namespace CloudCastle\Core\DataBase;

abstract class Migrate
{
    /**
     * @return string
     */
    protected static function table (): string
    {
        return static::$model::table();
    }
    
    /**
     * @return bool
     */
    abstract public function up (): bool;
    
    /**
     * @return bool
     */
    abstract public function down (): bool;
}