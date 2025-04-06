<?php

namespace CloudCastle\Core;

use CloudCastle\Core\Traits\SingletonTrait;
use DateTime;
use Exception;

final class Env
{
    use SingletonTrait;
    
    private array $data = [];
    
    /**
     * @param string $file
     * @return void
     * @throws Exception
     */
    public function init (string $file): void
    {
        if (!file_exists($file)) {
            throw new Exception("File '{$file}' does not exist");
        }
        
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }
            
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            if (preg_match('/\$\{([\w.]+)\}/', $value, $matches)) {
                $envVar = $matches[1];
                
                if ($var = $this->get($envVar)) {
                    if ($var instanceof DateTime) {
                        $var = $var->format($this->get('APP_DATE_DEFAULT_FORMAT', 'Y-m-d H:i:s'));
                    }
                    
                    $value = str_replace('${' . $envVar . '}', $var, $value);
                }
            }
            
            $this->set($key, trim($value, " \t\n\r\0\x0B'\""));
        }
    }
    
    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get (string $key, mixed $default = null): mixed
    {
        $key = mb_strtoupper($key);
        
        return $this->setType($this->data[$key] ?? $default);
    }
    
    private function setType (mixed $value): mixed
    {
        if (empty($value)) {
            return null;
        }
        
        if (in_array(mb_strtolower($value), ['yes', 'true', 'on', '1', 1])) {
            return true;
        }
        
        if (in_array(mb_strtolower($value), ['no', 'false', 'off', '0', 0])) {
            return false;
        }
        
        if (is_numeric($value)) {
            if (str_contains($value, '.')) {
                return floatval($value);
            }
            
            return (int) $value;
        }
        
        return (string) $value;
    }
    
    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set (string $key, mixed $value): self
    {
        $key = mb_strtoupper($key);
        putenv("$key=$value");
        $this->data[$key] = $value;
        $_ENV[$key] = $this->data[$key];
        
        return $this;
    }
}