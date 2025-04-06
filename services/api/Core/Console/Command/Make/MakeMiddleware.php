<?php

namespace CloudCastle\Core\Console\Command\Make;

use CloudCastle\Core\Middleware\Middleware;
use Exception;

final class MakeMiddleware extends AbstractMake
{
    const string DESCRIPTION = 'Сгенерировать новый класс посредник';
    
    protected const string WORK_DIR = APP_ROOT . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Middleware';
    
    protected const string NAME_SPASE = 'App\\Http\\Middleware';
    
    protected const string EXTENDS = Middleware::class;
    
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