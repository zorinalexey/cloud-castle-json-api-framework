<?php

namespace CloudCastle\Core\View;

use CloudCastle\Core\Router\Router;
use stdClass;
use Stringable;
use Throwable;

final class View extends stdClass implements Stringable
{
    /**
     * @var string
     */
    private readonly string $viewPath;
    
    /**
     * @var array
     */
    private array $data;
    
    /**
     * @param string $viewPath
     * @param mixed ...$data
     * @throws ViewException
     */
    public function __construct (string $viewPath, mixed ...$data)
    {
        $viewPath = APP_ROOT . '/resources/views/' . dirname($viewPath) . '/' . basename($viewPath) . '.php';
        
        if (!file_exists($viewPath)) {
            throw new ViewException("View '$viewPath' does not exist");
        }
        
        $this->viewPath = $viewPath;
        $this->data = [
            'config' => config(),
            'env' => env(),
            'route' => Router::getCurrentRoute(),
            'request' => Router::getRequest()
        ];
        
        foreach ($data as $items) {
            foreach ($items as $key => $value) {
                $this->data[$key] = $value;
            }
        }
    }
    
    /**
     * @return string
     * @throws ViewException
     */
    public function __toString (): string
    {
        return $this->render();
    }
    
    /**
     * @return string
     * @throws ViewException
     */
    public function render (): string
    {
        ob_start();
        $content = '';
        
        try {
            extract($this->data);
            include $this->viewPath;
            $content = ob_get_contents();
        } catch (Throwable $e) {
            throw new ViewException($e->getMessage(), $e->getCode(), $e);
        }
        
        ob_end_clean();
        
        return $content;
    }
}