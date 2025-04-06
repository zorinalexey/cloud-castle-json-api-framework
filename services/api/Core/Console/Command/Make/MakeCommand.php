<?php

namespace CloudCastle\Core\Console\Command\Make;

use CloudCastle\Core\Console\Command\Command;
use Exception;

final class MakeCommand extends AbstractMake
{
    public const string DESCRIPTION = 'Сгенерировать новый класс консольной команды';
    
    protected const string WORK_DIR = APP_ROOT . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Console';
    
    protected const string NAME_SPASE = 'App\\Console';
    
    protected const string EXTENDS = Command::class;
    
    /**
     * @return string|null
     * @throws Exception
     */
    public function handle (): string|null
    {
        $this->make($this->getMakeInfo());
        
        return null;
    }
}