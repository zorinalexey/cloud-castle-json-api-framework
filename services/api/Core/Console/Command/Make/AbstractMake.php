<?php

namespace CloudCastle\Core\Console\Command\Make;

use CloudCastle\Core\Console\Command\Command;
use Exception;

abstract class AbstractMake extends Command
{
    protected const string NAME_SPASE = 'App';
    
    protected const string WORK_DIR = APP_ROOT . DIRECTORY_SEPARATOR . 'App';
    
    protected const string EXTENDS = '';
    
    /**
     * @param array $makeInfo
     * @return void
     * @throws Exception
     */
    final protected function make (array $makeInfo): void
    {
        $tpl = $makeInfo['tpl'];
        
        if (!is_file($tpl)) {
            throw new Exception("Template file {$tpl} not found");
        }
        
        if (file_exists($makeInfo['file'])) {
            throw new Exception("File {$makeInfo['file']} already exists");
        }
        
        $content = file_get_contents($tpl);
        
        foreach ([...$this->arguments(), ...$makeInfo] as $index => $value) {
            $content = str_replace('{' . $index . '}', $value, $content);
        }
        
        if ($this->createDir($makeInfo['dir']) && file_put_contents($makeInfo['file'], $content) !== false) {
            $message = "Created {$makeInfo['class_name']}" . PHP_EOL . 'File: ' . $makeInfo['file'];
            $this->info($message);
            
            foreach ($this->getRelationTasks() as $task => $args) {
                $this->run($task, $args);
            }
            
            return;
        }
        
        throw new Exception("Unable to create {$makeInfo['class_name']}.");
    }
    
    /**
     * @param string $dir
     * @return bool
     * @throws Exception
     */
    private function createDir (string $dir): bool
    {
        if (is_dir($dir)) {
            return true;
        }
        
        if (!mkdir($dir, 0777, true)) {
            throw new Exception("Can't create directory {$dir}");
        }
        
        return true;
    }
    
    protected function getRelationTasks (): array
    {
        return [];
    }
    
    final protected function getMakeInfo (): array
    {
        $name = $this->argument('name', required : true);
        $extends = $this->argument('extends', default : static::EXTENDS);
        
        return [
            'name_spase' => $this->getNameSpase($name),
            'class_name' => $this->getClassName($name),
            'extends_class_name' => $this->getExtendsClassName($extends),
            'use_class_name' => $this->useClassName($extends),
            'file' => $this->getFile($name),
            'tpl' => $this->getTplFile(),
            'dir' => $this->getDir($name),
        ];
    }
    
    /**
     * @param string $name
     * @return string
     */
    private function getNameSpase (string $name): string
    {
        $class = static::NAME_SPASE . '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $name);
        
        return class_namespace($class);
    }
    
    /**
     * @param string $name
     * @return string
     */
    private function getClassName (string $name): string
    {
        $file = $this->getFile($name);
        
        return class_basename($file);
    }
    
    /**
     * @param string $name
     * @return string
     */
    private function getFile (string $name): string
    {
        return str_replace('\\', DIRECTORY_SEPARATOR, static::WORK_DIR . DIRECTORY_SEPARATOR . $name . '.php');
    }
    
    /**
     * @param string $extends
     * @return string
     */
    private function getExtendsClassName (string|null $extends): string
    {
        if ($extends === null || empty($extends)) {
            return '';
        }
        
        return ' extends ' . class_basename($extends);
    }
    
    private function useClassName (string|null $extends): string
    {
        if (!$extends) {
            return '';
        }
        
        return 'use ' . $extends . ';' . PHP_EOL;
    }
    
    /**
     * @return string
     */
    private function getTplFile (): string
    {
        $class = basename(str_replace('\\', DIRECTORY_SEPARATOR, static::class));
        
        return __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $class . '.ptpl';
    }
    
    /**
     * @param string $name
     * @return string
     */
    private function getDir (string $name): string
    {
        $file = $this->getFile($name);
        
        return dirname(str_replace('\\', DIRECTORY_SEPARATOR, $file));
    }
}