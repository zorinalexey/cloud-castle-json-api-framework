<?php

namespace CloudCastle\Core\Console;

use CloudCastle\Core\Console\Command\Command;
use CloudCastle\Core\Console\Command\CommandInterface;
use Exception;
use Throwable;

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
                unlink($class->getRunFile());
                throw new Exception("Class '{$class}' does not instance of " . Command::class);
            }
            
            try{
                $class->setLockFile();
                $message = $class->handle();
                
                if ($message && trim($message) !== '') {
                    echo TerminalColor::green($message) . PHP_EOL;
                }
            }catch(Throwable $e){
                unlink($class->getRunFile());
                echo TerminalColor::red($e->getMessage()) . PHP_EOL;
            }
            
            return;
        }
        
        throw new Exception("Command `{$this->command}` does not exist");
    }
    
    private function runHelp (): void
    {
        $color = TerminalColor::class;
        $commands = config('command');
        ksort($commands);
        $lastCommandName = ' ';
        
        foreach ($commands as $command => $class) {
            if (($newList = explode(':', $command)[0]) && $lastCommandName !== $newList) {
                echo PHP_EOL . $color::red($newList) . PHP_EOL;
            }
            
            echo $color::blue("$command ") . $color::green($this->getStr($command, $class::DESCRIPTION) . " " . $class::DESCRIPTION ). PHP_EOL;
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