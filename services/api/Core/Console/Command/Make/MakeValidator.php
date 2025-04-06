<?php

namespace CloudCastle\Core\Console\Command\Make;

use CloudCastle\Core\Validator\AbstractValidator;
use Exception;

final class MakeValidator extends AbstractMake
{
    public const string DESCRIPTION = 'Сгенерировать новый класс валидации данных';
    
    protected const string WORK_DIR = APP_ROOT . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Validators';
    
    protected const string NAME_SPASE = 'App\\Validators';
    
    protected const string EXTENDS = AbstractValidator::class;
    
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