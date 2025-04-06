<?php

namespace CloudCastle\Core\Console\Command\Make;

use Exception;

final class MakeRoute extends AbstractMake
{
    public const string DESCRIPTION = 'Сгенерировать новый файл маршрутов';
    
    protected const string WORK_DIR = APP_ROOT . DIRECTORY_SEPARATOR . 'routes';
    
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