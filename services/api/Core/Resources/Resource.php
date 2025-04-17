<?php

namespace CloudCastle\Core\Resources;

use CloudCastle\Core\Collections\AbstractCollection;
use CloudCastle\Core\Collections\Collection;
use CloudCastle\Core\Traits\StringableTrait;
use stdClass;

abstract class Resource extends stdClass
{
    use StringableTrait;
    
    /**
     * @var array|object
     */
    private array|object $data;
    
    private function __construct(array|object $data)
    {
        $this->data = $data;
    }
    
    /**
     * @return array
     */
    abstract public function toArray (): array;
    
    /**
     * @param array|object $data
     * @return self
     */
    public static function make (array|object $data): self
    {
        $obj = new static($data);
        
        foreach ($obj->toArray() as $key => $value) {
            $obj->{$key} = $value;
        }
        
        return $obj;
    }
    
    /**
     * @param array|AbstractCollection $items
     * @return Collection
     */
    public static function collection (array|object $items): AbstractCollection
    {
        $data = [];
        
        foreach ($items as $item) {
            $data[] = static::make ($item);
        }
        
        return Collection::make($data);
    }
    
    /**
     * @param $name
     * @return mixed
     */
    public function __get ($name): mixed
    {
        if(is_array($this->data) && isset($this->data[$name])){
            return $this->data[$name];
        }
        
        return $this->data->{$name}??null;
    }
    
    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        if(is_object($this->data) && method_exists($this->data, $name)){
            return $this->data->{$name}(...$arguments);
        }
        
        return null;
    }
}