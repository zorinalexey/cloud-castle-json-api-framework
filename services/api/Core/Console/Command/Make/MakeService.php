<?php

namespace CloudCastle\Core\Console\Command\Make;

use CloudCastle\Core\Service\Service;
use Exception;

final class MakeService extends AbstractMake
{
    const string DESCRIPTION = 'Сгенерировать новый сервисный класс для обработки бизнес-логики';
    
    protected const string WORK_DIR = APP_ROOT . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Services';
    
    protected const string NAME_SPASE = 'App\\Services';
    
    protected const string EXTENDS = Service::class;
    
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