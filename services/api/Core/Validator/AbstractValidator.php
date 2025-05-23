<?php

namespace CloudCastle\Core\Validator;

abstract class AbstractValidator implements ValidatorInterface
{
    protected string|null $error = null;
    
    public function getError (): string|null
    {
        return $this->error;
    }
    
    protected function setDefault (mixed &$var, array $params = []): bool
    {
        if (!$var && isset($params['default'])) {
            $var = $params['default'][0] ?? null;
            unset($params['default']);
            $this->validate($var, $params);
            
            return true;
        }
        
        return false;
    }
}