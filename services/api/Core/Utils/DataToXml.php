<?php

namespace CloudCastle\Core\Utils;

use XMLWriter;

final class DataToXml
{
    /**
     * @var object
     */
    private readonly object $data;
    
    /**
     * @var XMLWriter
     */
    private readonly XMLWriter $dom;
    private string $nameElement = 'root';
    /**
     * @var array|mixed
     */
    private mixed $parentValue = null;
    
    /**
     * @param array|object $data
     */
    public function __construct(array|object $data)
    {
        $this->data = (object)$data;
        $this->dom = new XMLWriter();
        $this->dom->openMemory();
    }
    
    public function convert(string $rootElement = 'root', string $version = '1.0', string $encoding = 'utf-8'): self
    {
        $this->nameElement = $rootElement;
        $this->dom->startDocument($version, $encoding);
        $this->dom->startElement($this->nameElement);
        $this->setDom($this->data);
        $this->dom->endElement();
        $this->dom->endDocument();
        
        return $this;
    }
    
    public function toXml(): string
    {
        return $this->dom->outputMemory();
    }
    
    private function setNameElement (int|string $key): void
    {
        $this->nameElement = is_string($key) ? $key : 'item';
    }
    
    private function setDom (array|object $data): void
    {
        foreach($data as $key => $value) {
            $this->setNameElement($key);
            $this->dom->startElement($this->nameElement);
            
            if(is_array($value) || is_object($value)) {
                $this->setDom($value);
            }else{
                $this->setElement($value);
            }
            
            $this->dom->endElement();
        }
    }
    
    /**
     * @param int|float|string|bool|null $value
     * @return void
     */
    private function setElement (int|float|string|bool|null $value): void
    {
        if(is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }
        
        $this->dom->text((string)$value);
    }
}