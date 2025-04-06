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
}