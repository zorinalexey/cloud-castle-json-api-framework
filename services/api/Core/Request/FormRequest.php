<?php

namespace CloudCastle\Core\Request;

use CloudCastle\Core\Validator\AbstractValidator;
use CloudCastle\Core\Validator\ValidationException;

abstract class FormRequest extends AbstractRequest
{
    /**
     * @var Request
     */
    public readonly Request $original;
    
    /**
     * @var array|mixed
     */
    private readonly array $validators;
    private array $errors = [];
    
    /**
     *
     */
    protected function __construct ()
    {
        $request = Request::getInstance();
        
        foreach ($request as $key => $value) {
            $this->{$key} = $value;
        }
        
        $this->original = $request;
        $this->validators = config('validator');
    }
    
    /**
     * @return array
     * @throws ValidationException
     */
    public function validated (): array
    {
        $data = [];
        
        foreach ($this->rules() as $propertyName => $rules) {
            $this->runValidate($this->{$propertyName}, $rules, $propertyName);
            $data[$propertyName] = $this->{$propertyName};
        }
        
        if($this->errors) {
            throw new ValidationException('Validation failed', $this->errors);
        }
        
        return $data;
    }
    
    /**
     * @return array
     */
    abstract public function rules (): array;
    
    /**
     * @param string $propertyName
     * @param array $rules
     * @return mixed
     */
    private function runValidate (mixed &$var, string $rules, string $key): void
    {
        foreach (explode('|', $rules) as $rule) {
            $opt = explode(':', $rule);
            $type = $opt[0];
            unset($opt[0]);
            /** @var AbstractValidator $validator */
            $validator = isset($this->validators[$type]) ? new $this->validators[$type]() : null;
            
            if($validator) {
                $validator->validate($var, $opt);
                
                if($error = $validator->getError()){
                    $this->errors[$key][$type] = $error;
                }
                
                if(str_contains($rules, 'nullable')) {
                    unset($this->errors[$key]);
                }
            }
        }
        
    }
}