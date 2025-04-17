<?php

namespace CloudCastle\Core\Traits;

use CloudCastle\Core\Utils\DataToXml;
use DOMException;
use JsonException;

trait StringableTrait
{
    /**
     * @return string
     * @throws JsonException
     */
    public function __toString(): string
    {
        $contentType = mb_strtolower(headers('Content-Type')?:'html');
        $dom = new DataToXml($this->toArray());
        
        return match ($contentType) {
            'application/json' => json_encode($this->toArray(), JSON_THROW_ON_ERROR|JSON_PRETTY_PRINT),
            'application/xml', 'text/xml' => $dom->convert()->toXml(),
            default => $dom->convert()->toXml(),
        };
    }
}