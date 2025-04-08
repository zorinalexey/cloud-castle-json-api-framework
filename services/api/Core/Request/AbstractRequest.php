<?php

namespace CloudCastle\Core\Request;

use CloudCastle\Core\Enums\VarType;
use stdClass;

abstract class AbstractRequest extends stdClass implements RequestInterface
{
    /**
     * @var array
     */
    protected static array $instance = [];
    
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
     * @param mixed|null $default
     * @param VarType|null $type
     * @return mixed
     */
    final public function get (string $name, mixed $default = null, VarType|null $type = null): mixed
    {
        $var = $this->{$name} ?? $default;
        
        if($type) {
            settype($var, $type->value);
        }
        
        $this->{$name} = $var;
        
        return $var;
    }
    
    /**
     * @param string $name
     * @return mixed
     */
    final public function __get(string $name): mixed
    {
        return $this->get($name);
    }
    
    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    final public function __set (string $name, mixed $value): void
    {
        $this->{$name} = $value;
    }
    
    /**
     * @param string|null $name
     * @return mixed
     */
    final public function headers(string|null $name = null): mixed
    {
        if(!$name) {
            return $this->headers;
        }
        
        foreach ($this->headers as $key => $value) {
            if (mb_strtolower($name) === mb_strtolower($key)) {
                return $value;
            }
        }
        
        return null;
    }
    
    /**
     * @param string|null $name
     * @return mixed
     */
    final public function server(string|null $name = null): mixed
    {
        if(!$name) {
            return $this->server;
        }
        
        foreach ($this->server as $key => $value) {
            if (mb_strtolower($name) === mb_strtolower($key)) {
                return $value;
            }
        }
        
        return null;
    }
    
    /**
     * @param string|null $name
     * @return mixed
     */
    final public function cookies(string|null $name = null): mixed
    {
        if(!$name) {
            return $this->cookies;
        }
        
        foreach ($this->cookies as $key => $value) {
            if (mb_strtolower($name) === mb_strtolower($key)) {
                return $value;
            }
        }
        
        return null;
    }
    
    /**
     * @param string|null $name
     * @return mixed
     */
    final public function session(string|null $name = null): mixed
    {
        if(!$name) {
            return $this->session;
        }
        
        foreach ($this->session as $key => $value) {
            if ($name === $key) {
                return $value;
            }
        }
        
        return null;
    }
    
    /**
     * @param string|null $name
     * @return mixed
     */
    final public function env(string|null $name = null): mixed
    {
        if(!$name) {
            return $this->env;
        }
        
        foreach ($this->env as $key => $value) {
            if ($name === $key) {
                return $value;
            }
        }
        
        return null;
    }
    
    /**
     * @param string|null $name
     * @return mixed
     */
    final public function files(string|null $name = null): mixed
    {
        if(!$name) {
            return $this->files;
        }
        
        foreach ($this->files as $key => $value) {
            if ($name === $key) {
                return $value;
            }
        }
        
        return null;
    }
}