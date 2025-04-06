<?php

namespace CloudCastle\Core\Validator\Types;

use CloudCastle\Core\Validator\AbstractValidator;

final class EnumType extends AbstractValidator
{
    
    /**
     * @param mixed &$var
     * @param mixed|array $params
     * @return void
     */
    public function validate (mixed &$var, mixed $params = []): void
    {
        if ($this->setDefault($var, $params)) {
            return;
        }
        
        if (!in_array($var, $params['enum'])) {
            $this->error = 'Value is not a valid enum value: ' . implode(', ', $params['enum']);
        }
    }
}