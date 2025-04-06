<?php

namespace CloudCastle\Core\Console\Command\Make;

use CloudCastle\Core\Model\Model;
use Exception;

final class MakeModel extends AbstractMake
{
    const string DESCRIPTION = 'Сгенерировать новый класс модели';
    
    protected const string WORK_DIR = APP_ROOT . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Models';
    
    protected const string NAME_SPASE = 'App\\Models';
    
    protected const string EXTENDS = Model::class;
    
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