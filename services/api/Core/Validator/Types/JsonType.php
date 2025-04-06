<?php

namespace CloudCastle\Core\Validator\Types;

use CloudCastle\Core\Validator\AbstractValidator;

final class JsonType extends AbstractValidator
{
    
    /**
     * @param mixed &$var
     * @param mixed|array $params
     * @return void
     */
    public function validate (mixed &$var, mixed $params = []): void
    {
        if (is_string($var) && json_validate($var)) {
            $var = json_decode($var);
            
            return;
        }
        
        if ($this->setDefault($var, $params)) {
            return;
        }
        
        $this->error = 'Value is not a valid JSON string';
    }
}