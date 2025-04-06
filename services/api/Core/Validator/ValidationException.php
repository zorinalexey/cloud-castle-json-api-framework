<?php

namespace CloudCastle\Core\Validator;

use Exception;

final class ValidationException extends Exception
{
    private readonly array $errors;
    
    public function __construct($message, array $errors){
        $this->code = 10001;
        $this->errors = $errors;
        
        $this> $this->setFullMessage($message);
        
        parent::__construct($message, $this->code);
    }
    
    private function setFullMessage (string $message)
    {
        $this->message = $message;
        
        foreach ($this->errors as $key => $error) {
        
        }
    }
}