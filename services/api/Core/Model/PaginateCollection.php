<?php

namespace CloudCastle\Core\Model;

use CloudCastle\Core\Collections\AbstractCollection;

final class PaginateCollection extends AbstractCollection
{
    public static function make (array $items = []): static
    {
        $collection = new static($items['items']);
        unset($items['items']);
        
        foreach ($items as $key => $value) {
            $collection->{$key} = $value;
        }
        
        return $collection;
    }
    
}