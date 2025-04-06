<?php

namespace CloudCastle\Core\Validator\Types;

use CloudCastle\Core\Validator\AbstractValidator;

final class TimeType extends AbstractValidator
{
    
    /**
     * @param mixed &$var
     * @param mixed|array $params
     * @return void
     */
    public function validate (mixed &$var, mixed $params = []): void
    {
        if ($date = date_create($var)) {
            $format = 'H:i:s';
            
            if (isset($params['format'])) {
                $format = $params['format'];
            }
            
            $var = $date->format($format);
            
            return;
        }
        
        if ($this->setDefault($var, $params)) {
            return;
        }
        
        $this->error = 'Value is not a valid date';
    }
}