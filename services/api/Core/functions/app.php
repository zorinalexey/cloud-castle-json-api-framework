<?php

use CloudCastle\Core\App;

function app (string|null $key = null, mixed $default = null): mixed
{
    $app = App::getInstance();
    
    if (!$key) {
        return $app;
    }
    
    return $app->get($key);
}