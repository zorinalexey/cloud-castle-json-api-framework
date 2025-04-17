<?php

namespace CloudCastle\Core\Collections;

final class Collection extends AbstractCollection
{
    /**
     * @param array $items
     * @return static
     */
    public static function make (array $items = []): static
    {
        return new static($items);
    }
}