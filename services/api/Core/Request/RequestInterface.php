<?php

namespace CloudCastle\Core\Request;

interface RequestInterface
{
    /**
     * @return self
     */
    public static function getInstance (): self;
    
    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getVar (string $name, mixed $default = null): mixed;
    
    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed;
    
    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set (string $name, mixed $value): void;
    
    /**
     * @param string|null $name
     * @return mixed
     */
    public function headers(string|null $name = null): mixed;
    
    /**
     * @param string|null $name
     * @return mixed
     */
    public function server(string|null $name = null): mixed;
    
    /**
     * @param string|null $name
     * @return mixed
     */
    public function cookies(string|null $name = null): mixed;
    
    /**
     * @param string|null $name
     * @return mixed
     */
    public function session(string|null $name = null): mixed;
    
    /**
     * @param string|null $name
     * @return mixed
     */
    public function env(string|null $name = null): mixed;
    
    /**
     * @param string|null $name
     * @return mixed
     */
    public function files(string|null $name = null): mixed;
}