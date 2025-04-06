<?php

namespace Core\Validator\Types;

use CloudCastle\Core\Validator\AbstractValidator;

final class EmailType extends AbstractValidator
{
    
    /**
     * @param mixed &$var
     * @param mixed|array $params
     * @return void
     */
    public function validate (mixed &$var, mixed $params = []): void
    {
        $str = trim((string) $var);
        
        if ($email = filter_var($str, FILTER_VALIDATE_EMAIL)) {
            $var = $email;
            
            return;
        }
        
        if ($this->setDefault($var, $params)) {
            
            return;
        }
        
        $this->error = "Email is not valid";
    }
}