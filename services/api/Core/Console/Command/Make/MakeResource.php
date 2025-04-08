<?php

namespace CloudCastle\Core\Console\Command\Make;

use CloudCastle\Core\Resources\Resource;
use Exception;

final class MakeResource extends AbstractMake
{
    const string DESCRIPTION = 'Сгенерировать новый класс ресурса представления данных';
    
    protected const string WORK_DIR = APP_ROOT . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Resources';
    
    protected const string NAME_SPASE = 'App\\Http\\Resources';
    
    protected const string EXTENDS = Resource::class;
    
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