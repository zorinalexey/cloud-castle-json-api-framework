<?php

use CloudCastle\Core\Config;

/**
 * @param string|null $key
 * @param mixed|null $default
 * @return mixed
 */
function config (string|null $key, mixed $default = null): mixed
{
    $config = Config::getInstance();
    
    if (!$key) {
        return $config;
    }
    
    return $config->get($key, $default);
}