<?php

namespace CloudCastle\Core\Resources;

use CloudCastle\Core\Model\AbstractCollection;
use CloudCastle\Core\Model\Model;

abstract class Resource
{
    private array|Model $data = [];
    
    abstract public function toArray (): array;
    
    public static function make (array|Model $data): array
    {
        $object = new static();
        $object->data = $data;
        
        return $object->toArray();
    }
    
    public function collection (array|AbstractCollection $items): array
    {
        $data = [];
        
        foreach ($items as $item) {
            $data[] = static::make ($item);
        }
        
        return $data;
    }
    
    public function __get ($name): mixed
    {
        if(is_array($this->data) && array_key_exists($name, $this->data)){
            return $this->data[$name];
        }
        
        return $this->data->{$name};
    }
    
    public function __call(string $name, array $arguments): mixed
    {
        if($this->data instanceof Model && method_exists($this->data, $name)){
            return $this->data->{$name}(...$arguments);
        }
        
        return null;
    }
}