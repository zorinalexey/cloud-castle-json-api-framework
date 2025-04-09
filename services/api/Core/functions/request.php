<?php

use CloudCastle\Core\Router\Router;

function getRequestUriWithoutQuery (): string
{
    $requestUri = $_SERVER['REQUEST_URI']??'/';
    
    if (($questionMarkPosition = strpos($requestUri, '?')) !== false) {
        $requestUri = substr($requestUri, 0, $questionMarkPosition);
    }
    
    return $requestUri;
}

/**
 * @param string $key
 * @return string
 */
function headers (string $key): string
{
    return Router::getRequest()->headers($key);
}

/**
 * @param string $key
 * @return mixed
 */
function post (string $key): mixed
{
    return Router::getRequest()->post($key);
}

/**
 * @param string $key
 * @return mixed
 */
function get (string $key): mixed
{
    return Router::getRequest()->get($key);
}