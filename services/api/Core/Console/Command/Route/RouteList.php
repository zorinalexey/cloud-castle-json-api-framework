<?php

namespace CloudCastle\Core\Console\Command\Route;

use CloudCastle\Core\Collections\Collection;
use CloudCastle\Core\Console\Command\Command;
use CloudCastle\Core\Console\TerminalColor;
use CloudCastle\Core\Router\Route;
use CloudCastle\Core\Router\Router;

final class RouteList extends Command
{
    const string DESCRIPTION = 'Просмотр списка зарегистрированных маршрутов';
    
    /**
     * @return string|null
     */
    public function handle (): string|null
    {
        $key = 1;
        
        Router::routes()->each(function (array $routes, string $method) use (&$key) {
            if($method !== 'OPTIONS') {
                echo PHP_EOL;
                
                Collection::make($routes)->each(function (Route $route) use ($method, &$key) {
                    echo "\t".($key). ') '.$this->getPathName($route).PHP_EOL;
                    $key++;
                });
            }
        });
        
        return null;
    }
    
    /**
     * @param Route $route
     * @return string
     */
    private function getPathName (Route $route): string
    {
        $color = TerminalColor::class;
        $str = "method: ".$color::green($route->getMethod())."\n\t";
        $str .= "   path: ".$color::green($route->getPath())."\n\t";
        $str .= "   pattern: ".$color::red($route->getPattern())."\n\t";
        $str .= "   controller: ".$color::green($route->getController())."\n\t";
        $str .= "   action: ".$color::green($route->getAction())."\n\t";
        $str .= "   request: ".$color::magenta($route->getRequest()::class);
        
        if($name = $route->getName()) {
            $str .= "\n\t   name: ".$color::blue($name);
            $str .= "\n\t   description: ".$color::green($route->getDescription());
        }
        
        if($middlewares = $route->getMiddlewares()) {
            $str .= "\n\t   middlewares: ". $color::green(implode(', ', $middlewares));
        }
        
        return $str;
    }
}