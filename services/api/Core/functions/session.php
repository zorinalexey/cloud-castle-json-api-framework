<?php

use CloudCastle\Core\Request\Request;

function session(string $key, mixed $default = null): mixed
{
    $request = Request::getInstance();
    $session = $request->session();
    
    return $session[$key]??$default;
}

function cookies(string $key, mixed $default = null): mixed
{
    $request = Request::getInstance();
    $cookies = $request->cookies();
    
    return $cookies[$key]??$default;
}