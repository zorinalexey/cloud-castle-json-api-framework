<?php

namespace CloudCastle\Core\Console\Command\Make;

use CloudCastle\Core\Repository\Repository;
use Exception;

final class MakeRepository extends AbstractMake
{
    const string DESCRIPTION = 'Сгенерировать новый класс репозитория';
    
    protected const string WORK_DIR = APP_ROOT . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Repository';
    
    protected const string NAME_SPASE = 'App\\Repository';
    
    protected const string EXTENDS = Repository::class;
    
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