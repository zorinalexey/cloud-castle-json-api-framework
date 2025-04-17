<?php

use CloudCastle\Core\Validator\AbstractValidator;
use CloudCastle\Core\Validator\ValidationException;

/**
 * @param mixed $var
 * @param string $rules
 * @return void
 * @throws ValidationException
 */
function validated (mixed &$var, string $rules): void
{
    $validators = config('validator');
    
    foreach (explode('|', $rules) as $rule) {
        $opt = explode(':', $rule);
        $type = $opt[0];
        unset($opt[0]);
        /** @var AbstractValidator $validator */
        $validator = isset($validators[$type]) ? new $validators[$type]() : null;
        $options = [];
        
        foreach ($opt as $value) {
            $optData = explode(',', $value);
            $key = $optData[0];
            unset($optData[0]);
            $options[$key] = [...$optData];
        }
        
        if ($validator) {
            $validator->validate($var, $options);
            
            if ($error = $validator->getError()) {
                $errors[$type] = $error;
            }
            
            if (str_contains($rules, 'nullable')) {
                unset($errors[$type]);
            }
            
            unset($validator, $options, $opt, $optData, $type);
            
            if ($error) {
                throw new ValidationException('Validation failed', $errors);
            }
        }
    }
}