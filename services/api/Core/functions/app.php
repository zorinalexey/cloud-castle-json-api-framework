<?php

use CloudCastle\Core\App;

/**
 * @param string|null $key
 * @param mixed|null $default
 * @return mixed
 */
function app (string|null $key = null, mixed $default = null): mixed
{
    $app = App::getInstance();
    
    if (!$key) {
        return $app;
    }
    
    return $app->get($key);
}