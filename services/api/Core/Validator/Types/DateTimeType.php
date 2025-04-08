<?php

namespace CloudCastle\Core\Validator\Types;

use CloudCastle\Core\Validator\AbstractValidator;

final class DateTimeType extends AbstractValidator
{
    
    /**
     * @param mixed &$var
     * @param mixed|array $params
     * @return void
     */
    public function validate (mixed &$var, mixed $params = []): void
    {
        if (!$var && $this->setDefault($var, $params)) {
            return;
        }
        
        if ($date = date_create($var)) {
            $var = $date;
            
            return;
        }
        
        $this->error = 'Value is not a valid date';
    }
}