<?php

namespace CloudCastle\Core\Filters;

use CloudCastle\Core\DataBase\Builder\Select;
use CloudCastle\Core\Model\ModelInterface;

abstract class AbstractFilter
{
    /**
     * @var string
     */
    protected readonly string $table;
    /**
     * @var Select
     */
    private Select $builder;
    
    /**
     * @param ModelInterface $model
     */
    final protected function __construct (ModelInterface $model)
    {
        $this->builder = new Select($model);
        $this->table = $model::table();
    }
    
    /**
     * @param ModelInterface $model
     * @param array $filters
     * @return Select
     */
    final public static function apply (ModelInterface $model, array $filters): Select
    {
        $obj = new static($model);
        $obj->setDefaultParams($filters, $model);
        
        foreach ($filters as $method => $value) {
            if (method_exists($obj, $method)) {
                $obj->{$method}($value);
            }
        }
        
        return $obj->builder;
    }
    
    /**
     * @param array $filters
     * @param ModelInterface $model
     * @return void
     */
    private function setDefaultParams (array &$filters, ModelInterface $model)
    {
        if (!isset($filters['trashed'])) {
            $filters['trashed'] = null;
        }
        
        if (!isset($filters['sort'])) {
            $column = 'id';
            
            if (in_array('name', $model::getColumns())) {
                $column = 'name';
            }
            
            $filters['sort'][$column] = 'ASC';
        }
    }
    
    /**
     * @param string|null $trashed
     * @return $this
     */
    final protected function trashed (string|null $trashed = null): self
    {
        $trashed = match ($trashed) {
            'only', 'trashed' => 'IS NOT NULL',
            'all' => null,
            default => 'IS NULL',
        };
        
        if ($trashed) {
            $this->builder->whereRaw("{$this->table}.deleted_at {$trashed}");
        }
        
        return $this;
    }
    
    /**
     * @param array $sorts
     * @return $this
     */
    final protected function sort (array $sorts): self
    {
        foreach ($sorts as $field => $direction) {
            if (method_exists($this, $method = "sort_{$field}")) {
                $this->{$method}($direction);
            } else {
                $this->builder->orderBy($field, $direction);
            }
        }
        
        return $this;
    }
    
    /**
     * @param string|null $direction
     * @return self
     */
    final protected function sort_id (string|null $direction): self
    {
        $this->builder->orderBy("{$this->table}.id", $direction);
        
        return $this;
    }
    
    /**
     * @param string|null $direction
     * @return self
     */
    final protected function sort_uuid (string|null $direction): self
    {
        $this->builder->orderBy("{$this->table}.uuid", $direction);
        
        return $this;
    }
    
    /**
     * @param string|null $direction
     * @return self
     */
    final protected function sort_created_at (string|null $direction): self
    {
        $this->builder->orderBy("{$this->table}.created_at", $direction);
        
        return $this;
    }
    
    /**
     * @param string|null $direction
     * @return self
     */
    final protected function sort_updated_at (string|null $direction): self
    {
        $this->builder->orderBy("{$this->table}.updated_at", $direction);
        
        return $this;
    }
    
    /**
     * @param string|null $direction
     * @return self
     */
    final protected function sort_deleted_at (string|null $direction): self
    {
        $this->builder->orderBy("{$this->table}.deleted_at", $direction);
        
        return $this;
    }
    
    /**
     * @param string|null $direction
     * @return self
     */
    final protected function sort_name (string|null $direction): self
    {
        $this->builder->orderBy("{$this->table}.name", $direction);
        
        return $this;
    }
}