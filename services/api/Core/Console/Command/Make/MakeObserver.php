<?php

namespace CloudCastle\Core\Console\Command\Make;

use CloudCastle\Core\Observer\Observer;
use Exception;

final class MakeObserver extends AbstractMake
{
    const string DESCRIPTION = 'Сгенерировать новый класс наблюдатель';
    
    protected const string WORK_DIR = APP_ROOT . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Observers';
    
    protected const string NAME_SPASE = 'App\\Observers';
    
    protected const string EXTENDS = Observer::class;
    
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