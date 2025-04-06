<?php

namespace CloudCastle\Core\Traits;

trait SingletonTrait
{
    /**
     * @var array
     */
    private static array $instances = [];
    
    /**
     *
     */
    final private function __construct ()
    {
    
    }
    
    /**
     * @return static
     */
    final public static function getInstance (): static
    {
        $class = static::class;
        
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }
        
        return self::$instances[$class];
    }
    
    /**
     * @return void
     */
    final public function __wakeup ()
    {
    
    }
    
    /**
     * @return void
     */
    private function __clone ()
    {
    
    }
}