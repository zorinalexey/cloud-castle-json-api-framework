<?php

namespace CloudCastle\Core\Console\Command;

use InvalidArgumentException;

abstract class Command implements CommandInterface
{
    private array $arguments;
    
    /**
     * @param array $arguments
     */
    final public function __construct (array $arguments = [])
    {
        $this->arguments = $arguments;
    }
    
    /**
     * @return bool
     */
    public static function checkRun (): bool
    {
        $file = static::getRunFile();
        $check = false;
        
        if (static::NOT_RUN_DUPLICATE) {
            $check = file_exists($file);
            
        }
        
        if ($check) {
            file_put_contents(static::getRunFile(), static::info('Task ' . static::class . ' run: ' . date('Y-m-d H:i:s')), FILE_APPEND | LOCK_EX);
        }
        
        return $check;
    }
    
    /**
     * @return string
     */
    private static function getRunFile (): string
    {
        return APP_ROOT . '/storage/app/command/' . md5(static::class) . '.run';
    }
    
    /**
     * @param string $message
     * @return string
     */
    final protected static function info (string $message): string
    {
        $message = $message . PHP_EOL;
        echo $message;
        
        return $message;
    }
    
    /**
     *
     */
    public function __destruct ()
    {
        if (($file = $this->getRunFile()) && file_exists($file)) {
            unlink($file);
        }
    }
    
    /**
     * @return void
     */
    public function setLockFile (): void
    {
        file_put_contents($this->getRunFile(), static::info('Task ' . static::class . ' run: ' . date('Y-m-d H:i:s')), FILE_APPEND | LOCK_EX);
    }
    
    /**
     * @param string $key
     * @param mixed|null $default
     * @param bool $required
     * @return mixed
     */
    final protected function argument (string $key, mixed $default = null, bool $required = false): mixed
    {
        if ($default !== null && !isset($this->arguments[$key])) {
            $this->arguments[$key] = $default;
        }
        
        if ($required && (!isset($this->arguments[$key]) || $this->arguments[$key] === null)) {
            throw new InvalidArgumentException("Argument '$key' is required");
        }
        
        return $this->arguments[$key] ?? $default;
    }
    
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function setArgument (string $key, string|int|float $value): void
    {
        $this->arguments[$key] = $value;
    }
    
    /**
     * @param string $command
     * @param array $arguments
     * @return void
     */
    final protected function run (string $command, array $arguments = []): void
    {
        command($command, $arguments);
    }
    
    /**
     * @return array
     */
    final protected function validated (): array
    {
        $args = [];
        
        foreach ($this->rules() as $key => $value) {
            $args[$key] = $value;
        }
        
        return $args;
    }
    
    protected function rules (): array
    {
        $data = [];
        
        foreach ($this->arguments() as $key => $value) {
            $data[$key] = 'string|nullable';
        }
        
        return $data;
    }
    
    /**
     * @return array
     */
    final protected function arguments (): array
    {
        return $this->arguments;
    }
}