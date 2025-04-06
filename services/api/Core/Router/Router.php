<?php

namespace CloudCastle\Core\Router;

use CloudCastle\Core\Controllers\Controller;
use CloudCastle\Core\Controllers\ErrorController;
use CloudCastle\Core\Middleware\Middleware;
use CloudCastle\Core\Request\Request;
use CloudCastle\Core\Request\RequestInterface;
use Exception;

final class Router
{
    private static Route|null $currentRoute = null;
    
    /**
     * @return Route
     * @throws Exception
     */
    public static function getCurrentRoute(): Route
    {
        if (!is_null(self::$currentRoute)) {
            return self::$currentRoute;
        }
        
        throw new Exception('Current route not defined');
    }
    
    /**
     * @return mixed
     */
    public static function run (): mixed
    {
        $request = Request::getInstance();
        
        /** @var Route $route */
        foreach (self::getRoutes() as $route) {
            if (preg_match($route->getPattern(), $request->request_uri, $matches)) {
                self::$currentRoute = $route;
                $controller = self::checkController($route->getController());
                $action = self::checkAction($controller, $route->getAction());
                $routeRequest = self::checkRequest($matches, $route->getRequest());
                
                
                if (self::checkMiddlewares($route->getMiddlewares(), $routeRequest)) {
                    return  $controller->{$action}($routeRequest);
                }
            }
        }
        
        $class = config('app.error_controller', ErrorController::class);
        $controller = self::checkController($class);
        $action = self::checkAction($controller, config('app.error_action', 'page404'));
        
        return $controller->{$action}($request);
    }
    
    /**
     * @return array
     */
    private static function getRoutes (): array
    {
        $allRoutes = Route::getRoutes();
        
        return $allRoutes[$_SERVER['REQUEST_METHOD']] ?? [];
    }
    
    /**
     * @param string $controller
     * @return Controller
     * @throws Exception
     */
    public static function checkController (string $controller): Controller
    {
        if (class_exists($controller) && ($controller = new $controller()) && $controller instanceof Controller) {
            return $controller;
        }
        
        throw new Exception("Controller {$controller} does not exist");
    }
    
    /**
     * @param Controller $controller
     * @param string $action
     * @return string
     * @throws Exception
     */
    public static function checkAction (Controller $controller, string $action): string
    {
        if (method_exists($controller, $action)) {
            return $action;
        }
        
        throw new Exception("Action {$action} does not exist");
    }
    
    /**
     * @param array $matches
     * @param RequestInterface $request
     * @return RequestInterface
     */
    private static function checkRequest (array $matches, RequestInterface $request)
    {
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $request->{$key} = $value;
            }
        }
        
        return $request;
    }
    
    /**
     * @param array $middlewares
     * @param RequestInterface $request
     * @return bool
     * @throws Exception
     */
    private static function checkMiddlewares (array $middlewares, RequestInterface $request): bool
    {
        if (count($middlewares) === 0) {
            return true;
        }
        
        $checks = [];
        
        foreach ($middlewares as $middleware) {
            if (class_exists($middleware) && ($middleware = new $middleware()) && $middleware instanceof Middleware) {
                $checks[] = $middleware->handle($request);
            } else {
                throw new Exception("Middleware {$middleware} does not exist");
            }
        }
        
        return !in_array(false, $checks);
    }
}