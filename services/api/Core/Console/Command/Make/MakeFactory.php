<?php

namespace CloudCastle\Core\Console\Command\Make;

use CloudCastle\Core\DataBase\Factory;
use Exception;

final class MakeFactory extends AbstractMake
{
    const string DESCRIPTION = 'Сгенерировать новый класс фабрики модели';
    
    protected const string WORK_DIR = APP_ROOT . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'factory';
    
    protected const string NAME_SPASE = 'DataBase\\Factory';
    
    protected const string EXTENDS = Factory::class;
    
    /**
     * @return string|null
     * @throws Exception
     */
    public function handle (): string|null
    {
        $this->argument('seed', required : true);
        $this->make($this->getMakeInfo());
        
        return null;
    }
}