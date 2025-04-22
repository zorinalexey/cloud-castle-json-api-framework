<?php

namespace CloudCastle\Core\Model;


use CloudCastle\Core\Collections\AbstractCollection;

final class Collection extends AbstractCollection
{
    public static function make (array $items = []): static
    {
        return new static($items);
    }
}