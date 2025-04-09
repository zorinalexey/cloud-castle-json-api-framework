<?php

namespace CloudCastle\Core;

use CloudCastle\Core\Traits\SingletonTrait;

final class Lang
{
    use SingletonTrait;
    
    /**
     * @var array
     */
    private array $lang = [];
    private string|null $key = null;
    
    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get (string $key, mixed $default = null): mixed
    {
        $current = &$this->lang[$this->key];
        
        foreach (explode('.', $key) as $key) {
            if (array_key_exists($key, $current)) {
                $current = &$current[$key];
            } else {
                return $default;
            }
        }
        
        return $current;
    }
    
    /**
     * @param string $dir
     * @return void
     */
    public function init (string $dir): void
    {
        $files = scan_dir($dir);
        $this->key = md5($dir);
        
        foreach ($files as $file) {
            $key = basename($file, '.php');
            $this->set($key, require_once $file);
        }
    }
    
    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set (string $key, mixed $value): self
    {
        $current = &$this->lang[$this->key];
        
        foreach (explode('.', $key) as $k) {
            if (!isset($current[$k])) {
                $current[$k] = [];
            }
            
            $current = &$current[$k];
        }
        
        $current = $value;
        
        return $this;
    }
    
    public function checkInit(): bool
    {
        return count($this->lang[$this->key]??[]) > 0;
    }
}