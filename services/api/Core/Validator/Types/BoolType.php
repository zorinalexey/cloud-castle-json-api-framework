<?php

namespace CloudCastle\Core\Validator\Types;

use CloudCastle\Core\Validator\AbstractValidator;

final class BoolType extends AbstractValidator
{
    
    /**
     * @param mixed &$var
     * @param mixed|array $params
     * @return void
     */
    public function validate (mixed &$var, mixed $params = []): void
    {
        if (is_string($var) && in_array($var, ['true', 'yes', 'on', '1'])) {
            $var = true;
            
            return;
        }
        
        if (is_string($var) && in_array($var, ['false', 'no', 'off', '0', 'null'])) {
            $var = false;
            
            return;
        }
        
        if (is_numeric($var) && $var > 0) {
            $var = true;
            
            return;
        }
        
        if (is_numeric($var) && $var <= 0) {
            $var = false;
            
            return;
        }
        
        if (is_array($var) && count($var) === 0) {
            $var = false;
            
            return;
        }
        
        if (is_array($var) && count($var) >= 1) {
            $var = true;
            
            return;
        }
        
        if ($this->setDefault($var, $params)) {
            return;
        }
        
        $var = filter_var($var, FILTER_VALIDATE_BOOLEAN);
    }
}