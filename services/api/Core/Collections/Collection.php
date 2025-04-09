<?php

namespace CloudCastle\Core\Collections;

final class Collection extends AbstractCollection
{
    
    public static function make (array $items = []): static
    {
        return new static($items);
    }
}