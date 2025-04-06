<?php

namespace CloudCastle\Core\Request;

final class Request extends AbstractRequest
{
    /**
     * @var Request|null
     */
    private static self|null $instance = null;
    
    /**
     * @var string
     */
    public readonly string $request_uri;
    
    /**
     *
     */
    protected function __construct ()
    {
        self::$instance = $this;
        
        foreach ($this->getData() as $key => $value) {
            $this->{$key} = $value;
        }
        
        $this->request_uri = getRequestUriWithoutQuery();
    }
    
    /**
     * @return array
     */
    private function getData (): array
    {
        $data = [];
        $headers = getallheaders();
        $contentType = isset($headers['Content-Type']) ? $headers['Content-Type'] : ($_SERVER['CONTENT_TYPE'] ?? null);
        
        if (($input = file_get_contents('php://input'))) {
            if ($contentType === 'application/json') {
                $data = json_decode($input, true);
            }
            
            if ($contentType === 'application/xml') {
                $data = (array) simplexml_load_string($input);
            }
        }
        
        $default = [...$_GET, 'session' => $_SESSION, 'cookie' => $_COOKIE, 'server' => $_SERVER, 'headers' => $headers, 'env' => $_ENV];
        
        return match ($_SERVER['REQUEST_METHOD']) {
            'POST', 'PUT', 'PATCH' => [...$default, ...$data, ...$_POST, 'files' => $_FILES],
            'DELETE' => [...$default, ...$data],
            default => $default,
        };
    }
}