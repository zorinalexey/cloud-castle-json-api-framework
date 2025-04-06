<?php

use CloudCastle\Core\Env;

function env (string|null $key = null, mixed $default = null): mixed
{
    $env = Env::getInstance();
    
    if (!$key) {
        return $env;
    }
    
    return $env->get($key, $default);
}