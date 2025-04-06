<?php

namespace CloudCastle\Core\Console\Command\Make;

use Exception;

final class MakeClass extends AbstractMake
{
    const string DESCRIPTION = 'Сгенерировать новый пустой класс';
    
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