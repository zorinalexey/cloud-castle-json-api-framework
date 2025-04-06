<?php

namespace CloudCastle\Core\Console\Command\Make;

use CloudCastle\Core\DataBase\Seed;
use Exception;

final class MakeSeed extends AbstractMake
{
    const string DESCRIPTION = 'Сгенерировать новый класс наполнения таблиц';
    
    protected const string WORK_DIR = APP_ROOT . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'seed';
    
    protected const string NAME_SPASE = 'DataBase\\Seed';
    
    protected const string EXTENDS = Seed::class;
    
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