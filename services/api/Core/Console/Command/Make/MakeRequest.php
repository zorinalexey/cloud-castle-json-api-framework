<?php

namespace CloudCastle\Core\Console\Command\Make;

use CloudCastle\Core\Request\FormRequest;
use Exception;

final class MakeRequest extends AbstractMake
{
    const string DESCRIPTION = 'Сгенерировать новый класс обработки HTTP запроса';
    
    protected const string WORK_DIR = APP_ROOT . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Request';
    
    protected const string NAME_SPASE = 'App\\Http\\Request';
    
    protected const string EXTENDS = FormRequest::class;
    
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