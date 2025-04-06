<?php

namespace CloudCastle\Core\Request;

use stdClass;

abstract class AbstractRequest extends stdClass implements RequestInterface
{
    /**
     * @var array
     */
    private static array $instance = [];
    
    /**
     * @return self
     */
    final public static function getInstance (): static
    {
        $class = static::class;
        
        if (!isset(self::$instance[$class])) {
            self::$instance[$class] = new static();
        }
        
        return self::$instance[$class];
    }
    
    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    final public function get (string $name, mixed $default = null): mixed
    {
        return $this->{$name} ?? $default;
    }
    
    final public function __get(string $name): mixed
    {
        return null;
    }
}