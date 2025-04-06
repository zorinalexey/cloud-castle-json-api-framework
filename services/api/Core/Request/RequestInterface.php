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
    public function get (string $name, mixed $default = null): mixed;
}