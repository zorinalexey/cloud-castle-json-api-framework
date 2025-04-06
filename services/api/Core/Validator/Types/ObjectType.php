<?php

namespace CloudCastle\Core\Validator\Types;

use CloudCastle\Core\Validator\AbstractValidator;
use stdClass;

final class ObjectType extends AbstractValidator
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
        
        if (isset($params['object'])) {
            $obj = new stdClass();
            
            foreach ($params['object'] as $item) {
                [$key, $value] = explode('=', $item, 2);
                $obj->{$key} = $value;
            }
            
            $var = $obj;
        }
        
        $this->error = 'Object type not allowed';
    }
}