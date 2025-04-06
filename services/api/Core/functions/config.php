<?php

use CloudCastle\Core\Config;

function config (string|null $key, mixed $default = null): mixed
{
    $config = Config::getInstance();
    
    if (!$key) {
        return $config;
    }
    
    return $config->get($key, $default);
}