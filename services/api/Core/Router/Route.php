<?php

namespace CloudCastle\Core\Router;

use CloudCastle\Core\Request\Request;
use CloudCastle\Core\Request\RequestInterface;

final class Route
{
    /**
     * @var array
     */
    private static array $routes = [];
    
    /**
     * @var string
     */
    private readonly string $controller;
    
    /**
     * @var string
     */
    private readonly string $action;
    
    /**
     * @var string
     */
    private readonly string $path;
    
    /**
     * @var string
     */
    private readonly string $pattern;
    
    /**
     * @var string|null
     */
    private string|null $name = null;
    
    /**
     * @var string
     */
    private string $request = Request::class;
    
    /**
     * @var array
     */
    private array $middlewares = [];
    
    /**
     * @var string
     */
    private readonly string $method;
    
    /**
     * @param string $controller
     * @param string $action
     * @param string $path
     */
    private function __construct (string $path, string $controller, string $action, string $method = 'GET')
    {
        $this->controller = $controller;
        $this->action = $action;
        $this->path = '/'.trim($path, '/');
        $this->pattern = $this->getPatternForPath();
        $this->method = $method;
    }
    
    /**
     * @return string
     */
    private function getPatternForPath (): string
    {
        return '~^' . preg_replace(['~{(\w+)}~iu', '~(\*+)~ui'], ['(?<$1>[\w-]+)', '(.+)?'], $this->getPath()) . '$~ui';
    }
    
    /**
     * @return string
     */
    public function getPath (): string
    {
        return $this->path;
    }
    
    /**
     * @param string $path
     * @param string $controller
     * @param string $action
     * @return self
     */
    public static function delete (string $path, string $controller, string $action = '__invoke'): self
    {
        $route = new self($path, $controller, $action, 'DELETE');
        self::$routes['OPTIONS'][] = $route;
        self::$routes['DELETE'][] = $route;
        
        return $route;
    }
    
    /**
     * @param string $path
     * @param string $controller
     * @param string $action
     * @return self
     */
    public static function get (string $path, string $controller, string $action = '__invoke'): self
    {
        $route = new self($path, $controller, $action, 'GET');
        self::$routes['OPTIONS'][] = $route;
        self::$routes['GET'][] = $route;
        
        return $route;
    }
    
    /**
     * @param string $path
     * @param string $controller
     * @param string $action
     * @return self
     */
    public static function patch (string $path, string $controller, string $action = '__invoke'): self
    {
        $route = new self($path, $controller, $action, 'PATCH');
        self::$routes['OPTIONS'][] = $route;
        self::$routes['PATCH'][] = $route;
        
        return $route;
    }
    
    /**
     * @param string $path
     * @param string $controller
     * @param string $action
     * @return self
     */
    public static function post (string $path, string $controller, string $action = '__invoke'): self
    {
        $route = new self($path, $controller, $action, 'POST');
        self::$routes['OPTIONS'][] = $route;
        self::$routes['POST'][] = $route;
        
        return $route;
    }
    
    /**
     * @param string $path
     * @param string $controller
     * @param string $action
     * @return self
     */
    public static function put (string $path, string $controller, string $action = '__invoke'): self
    {
        $route = new self($path, $controller, $action, 'PUT');
        self::$routes['OPTIONS'][] = $route;
        self::$routes['PUT'][] = $route;
        
        return $route;
    }
    
    /**
     * @param string $path
     * @param string $controller
     * @param string $action
     * @return self
     */
    public static function view (string $path, string $controller, string $action = '__invoke'): self
    {
        $route = new self($path, $controller, $action, 'VIEW');
        self::$routes['OPTIONS'][] = $route;
        self::$routes['VIEW'][] = $route;
        
        return $route;
    }
    
    /**
     * @param string $controller
     * @param string $action
     * @return self
     */
    public static function error (int $code, string $controller, string $action = '__invoke', string|null $path = null): self
    {
        if (!$path) {
            $path = '*';
        }
        
        $route = new self($path, $controller, $action);
        $route->getRequest()->errorCode = $code;
        self::$routes['OPTIONS'][] = $route;
        self::$routes['DELETE'][] = $route;
        self::$routes['GET'][] = $route;
        self::$routes['PATCH'][] = $route;
        self::$routes['POST'][] = $route;
        self::$routes['PUT'][] = $route;
        self::$routes['VIEW'][] = $route;
        
        return $route;
    }
    
    /**
     * @return RequestInterface
     */
    public function getRequest (): RequestInterface
    {
        return $this->request::getInstance();
    }
    
    /**
     * @param string $path
     * @param string $controller
     * @param string $action
     * @return self
     */
    public static function any (string $path, string $controller, string $action = '__invoke'): self
    {
        $route = new self($path, $controller, $action);
        self::$routes['OPTIONS'][] = $route;
        self::$routes['DELETE'][] = $route;
        self::$routes['GET'][] = $route;
        self::$routes['PATCH'][] = $route;
        self::$routes['POST'][] = $route;
        self::$routes['PUT'][] = $route;
        self::$routes['VIEW'][] = $route;
        
        return $route;
    }
    
    /**
     * @return array
     */
    public static function getRoutes (): array
    {
        return self::$routes;
    }
    
    /**
     * @return string
     */
    public function getController (): string
    {
        return $this->controller;
    }
    
    /**
     * @return string
     */
    public function getAction (): string
    {
        return $this->action;
    }
    
    /**
     * @return string
     */
    public function getPattern (): string
    {
        return $this->pattern;
    }
    
    /**
     * @param string $name
     * @return $this
     */
    public function name (string $name): self
    {
        $this->name = mb_strtolower($this->method).'.'.$name;
        
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getName (): string|null
    {
        return $this->name;
    }
    
    /**
     * @param string $request
     * @return $this
     */
    public function request (string $request): self
    {
        $this->request = $request;
        
        return $this;
    }
    
    /**
     * @param string|array $middlewares
     * @return $this
     */
    public function middleware (string|array $middlewares): self
    {
        if (is_string($middlewares)) {
            $middlewares = [$middlewares];
        }
        
        $this->middlewares = $middlewares;
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function getMiddlewares (): array
    {
        return array_unique($this->middlewares);
    }
    
    /**
     * @return string
     */
    public function getMethod (): string
    {
        return $this->method;
    }
    
    /**
     * @return string
     */
    public function getDescription():string
    {
        return trans('routes.'.$this->name, [':path' => $this->path, ':controller' => $this->controller, ':action' => $this->action]);
    }
}