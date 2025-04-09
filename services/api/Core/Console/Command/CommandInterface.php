<?php

namespace CloudCastle\Core\Console\Command;

interface CommandInterface
{
    public const string DESCRIPTION = 'Description command';
    
    public const bool NOT_RUN_DUPLICATE = true;
    
    /**
     * @return string|null
     */
    public function handle (): string|null;
    
    /**
     * @param array $arguments
     */
    public function __construct (array $arguments = []);
    
    /**
     * @return bool
     */
    public static function checkRun (): bool;
    
    /**
     * @return void
     */
    public function setLockFile (): void;
}