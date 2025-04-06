<?php

namespace CloudCastle\Core;

use CloudCastle\Core\Traits\SingletonTrait;

final class App
{
    use SingletonTrait;
    
    private array $app = [];
    
    public function set (string $key, mixed $value): self
    {
        $this->app[$key] = $value;
        
        return $this;
    }
    
    public function get (string $key, mixed $default = null): mixed
    {
        return $this->app[$key] ?? $default;
    }
}