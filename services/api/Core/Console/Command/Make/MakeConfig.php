<?php

namespace CloudCastle\Core\Console\Command\Make;

use Exception;

final class MakeConfig extends AbstractMake
{
    public const string DESCRIPTION = 'Сгенерировать новый файл конфигурации';
    
    protected const string WORK_DIR = APP_ROOT . DIRECTORY_SEPARATOR . 'config';
    
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