<?php

use CloudCastle\Core\Request\Request;

/**
 * @param string $key
 * @param mixed|null $default
 * @return mixed
 */
function session(string $key, mixed $default = null): mixed
{
    $request = Request::getInstance();
    $session = $request->session();
    
    return $session[$key]??$default;
}

/**
 * @param string $key
 * @param mixed|null $default
 * @return mixed
 */
function cookies(string $key, mixed $default = null): mixed
{
    $request = Request::getInstance();
    $cookies = $request->cookies();
    
    return $cookies[$key]??$default;
}