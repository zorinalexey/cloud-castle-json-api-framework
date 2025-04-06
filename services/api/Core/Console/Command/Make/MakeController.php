<?php

namespace CloudCastle\Core\Console\Command\Make;

use CloudCastle\Core\Controllers\Controller;
use Exception;

final class MakeController extends AbstractMake
{
    public const string DESCRIPTION = 'Сгенерировать новый класс контроллера';
    
    protected const string WORK_DIR = APP_ROOT . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controllers';
    
    protected const string NAME_SPASE = 'App\\Http\\Controllers';
    
    protected const string EXTENDS = Controller::class;
    
    /**
     * @return string|null
     * @throws Exception
     */
    public function handle (): string|null
    {
        $this->argument('request', default : '');
        $this->make($this->getMakeInfo());
        
        return null;
    }
}