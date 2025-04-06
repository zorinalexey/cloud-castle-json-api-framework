<?php

namespace CloudCastle\Core\Validator\Types;

use CloudCastle\Core\Validator\AbstractValidator;

final class StringType extends AbstractValidator
{
    
    /**
     * @param mixed &$var
     * @param mixed|array $params
     * @return void
     */
    public function validate (mixed &$var, mixed $params = []): void
    {
        $var = trim((string) $var);
        
        if (empty($var)) {
            $this->setDefault($var, $params);
        }
    }
}