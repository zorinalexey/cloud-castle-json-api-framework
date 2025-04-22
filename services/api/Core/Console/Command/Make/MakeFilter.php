<?php

namespace CloudCastle\Core\Console\Command\Make;

use CloudCastle\Core\Filters\AbstractFilter;
use Exception;

final class MakeFilter extends AbstractMake
{
    public const string DESCRIPTION = 'Сгенерировать новый класс для фильтрации выборки данных';
    
    protected const string WORK_DIR = APP_ROOT . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Filters';
    
    protected const string NAME_SPASE = 'App\\Filters';
    
    protected const string EXTENDS = AbstractFilter::class;
    
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