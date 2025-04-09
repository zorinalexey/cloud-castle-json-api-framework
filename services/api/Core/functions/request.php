<?php

function getRequestUriWithoutQuery (): string
{
    $requestUri = $_SERVER['REQUEST_URI']??'/';
    
    if (($questionMarkPosition = strpos($requestUri, '?')) !== false) {
        $requestUri = substr($requestUri, 0, $questionMarkPosition);
    }
    
    return $requestUri;
}