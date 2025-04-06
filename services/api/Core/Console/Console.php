<?php

namespace CloudCastle\Core\Console;

use CloudCastle\Core\Console\Command\Command;
use Exception;

final class Console
{
    private string|null $command = null;
    private array $arguments = [];
    
    public function __construct (string|null $command = null, ...$arguments)
    {
        $this->command = $command;
        $this->arguments = $arguments;
    }
    
    /**
     * @return void
     * @throws Exception
     */
    public function run (): void
    {
        if (!$this->command) {
            $this->runHelp();
            return;
        }
        
        if (($class = config('command.' . $this->command)) && class_exists($class)) {
            if ($class::checkRun()) {
                return;
            }
            
            $class = new $class($this->arguments);
            
            if (!$class instanceof Command) {
                throw new Exception("Class '{$class}' does not instance of " . Command::class);
            }
            
            $class->setLockFile();
            $message = $class->handle();
            
            if ($message && trim($message) !== '') {
                echo $message . PHP_EOL;
            }
            
            return;
        }
        
        throw new Exception("Command `{$this->command}` does not exist");
    }
    
    private function runHelp (): void
    {
        $commands = config('command');
        ksort($commands);
        $lastCommandName = ' ';
        
        foreach ($commands as $command => $class) {
            if (($newList = explode(':', $command)[0]) && $lastCommandName !== $newList) {
                echo PHP_EOL . $newList . PHP_EOL;
            }
            
            echo "$command " . $this->getStr($command, $class::DESCRIPTION) . " " . $class::DESCRIPTION . PHP_EOL;
            $lastCommandName = explode(':', $command)[0];
        }
    }
    
    private function getStr (string $command, string $description): string
    {
        $str = '';
        $length = mb_strlen($command . $description);
        
        if ($length < 180) {
            for ($i = 0; $i < 90 - $length; $i++) {
                $str .= ' ';
            }
        }
        
        return $str;
    }
}