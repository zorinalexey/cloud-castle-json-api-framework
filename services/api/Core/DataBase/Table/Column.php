<?php

namespace CloudCastle\Core\DataBase\Table;

use CloudCastle\Core\Model\Model;

final class Column
{
    /**
     * @var string
     */
    private readonly string $column;
    /**
     * @var Model
     */
    private readonly string $model;
    
    /**
     * @param string $column
     * @param string $model
     */
    public function __construct (string $column, string $model)
    {
        $this->column = $column;
        $this->model = $model;
    }
    
    /**
     * @return $this
     */
    public function autoIncrement (): self
    {
        return $this;
    }
    
    /**
     * @param string $string
     * @return $this
     */
    public function comment (string $string): self
    {
        return $this;
    }
    
    /**
     * @return $this
     */
    public function dateTime (): self
    {
        return $this;
    }
    
    /**
     * @return $this
     */
    public function currentTimestamp (): self
    {
        return $this;
    }
    
    /**
     * @return $this
     */
    public function nullable (): self
    {
        return $this;
    }
    
    /**
     * @return self
     */
    public function uuid (): self
    {
        return $this->string(40)->unique();
    }
    
    /**
     * @return self
     */
    public function unique (): self
    {
        return $this;
    }
    
    /**
     * @param int $max
     * @return self
     */
    public function string (int $max = 255): self
    {
        return $this;
    }
    
    /**
     * @param mixed $default
     * @return $this
     */
    public function default (mixed $default): self
    {
        return $this;
    }
    
    /**
     * @return string
     */
    public function __toString (): string
    {
        return $this->column;
    }
}