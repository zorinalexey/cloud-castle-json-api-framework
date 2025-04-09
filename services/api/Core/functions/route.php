<?php

use CloudCastle\Core\Collections\Collection;
use CloudCastle\Core\Router\Route;
use CloudCastle\Core\Router\Router;

/**
 * @param string|null $name
 * @return Collection|Route|null
 */
function route(string|null $name = null): Collection|Route|null
{
    if(!$name){
        return Router::routes();
    }
    
    $obj = null;
    
    Router::routes()->each(function (array $routes) use ($name, &$obj) {
        Collection::make($routes)->each(function (Route $route) use (&$obj, $name) {
           if($name === $route->getName()) {
               $obj = $route;
           }
        });
    });
    
    return $obj;
}