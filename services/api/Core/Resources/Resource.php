<?php

namespace CloudCastle\Core\Resources;

use CloudCastle\Core\Collections\AbstractCollection;
use CloudCastle\Core\Model\Model;
use JsonException;

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
    
    public function __toString(): string
    {
        $contentType = headers('Content-Type');
        
        return match ($contentType) {
            'application/json' => $this->getToJson(),
            'application/xml', 'text/xml' => $this->getToXml(),
            default => $this->getToHtml(),
        };
    }
    
    /**
     * @return string
     * @throws JsonException
     */
    private function getToJson (): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR|JSON_PRETTY_PRINT);
    }
    
    /**
     * @return string
     */
    private function getToXml (): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>';
    }
    
    /**
     * @return string
     */
    private function getToHtml (): string
    {
        return (string) $this->data;
    }
}