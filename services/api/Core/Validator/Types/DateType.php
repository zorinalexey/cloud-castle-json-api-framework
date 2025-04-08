<?php

namespace CloudCastle\Core\Validator\Types;

use CloudCastle\Core\Validator\AbstractValidator;

final class DateType extends AbstractValidator
{
    
    /**
     * @param mixed &$var
     * @param mixed|array $params
     * @return void
     */
    public function validate (mixed &$var, mixed $params = []): void
    {
        $format = 'Y-m-d';
        
        if (isset($params['format'][0])) {
            $format = $params['format'][0];
        }
        
        if ($var && $date = date_create($var)) {
            $var = $date->format($format);
            
            return;
        }
        
        if ($this->setDefault($var, $params)) {
            return;
        }
        
        $this->error = 'Value is not a valid date';
    }
}