<?php

namespace CloudCastle\Core\Validator\Types;

use CloudCastle\Core\Validator\AbstractValidator;

final class FloatType extends AbstractValidator
{
    
    /**
     * @param mixed &$var
     * @param mixed|array $params
     * @return void
     */
    public function validate (mixed &$var, mixed $params = []): void
    {
        if (is_numeric($var) || is_float($var)) {
            $var = (float) $var;
            
            return;
        }
        
        if ($this->setDefault($var, $params)) {
            return;
        }
        
        $this->error = 'Value must be a number';
    }
}