<?php

namespace CloudCastle\Core\DataBase\Table;

use CloudCastle\Core\Model\Model;

abstract class Table
{
    /**
     * @var string
     */
    protected readonly string $table;
    
    /**
     * @var Model
     */
    protected string $model;
    
    /**
     * @var string
     */
    protected string $sql = '';
    
    /**
     * @var array
     */
    protected array $columns = [];
    
    /**
     * @param string $model
     */
    final public function __construct (string $model)
    {
        $this->model = $model;
        $this->table = $this->model::table();
    }
    
    /**
     * @return void
     */
    final public function drop (): void
    {
        $this->sql = /** @lang text */
            'DROP TABLE IF EXISTS ' . $this->table;
    }
    
    /**
     * @return string
     */
    final public function __toString (): string
    {
        return $this->toSql();
    }
    
    /**
     * @return string
     */
    abstract protected function toSql (): string;
    
    /**
     * @return void
     */
    final public function timestamp (): void
    {
        $this->column('created_at')->dateTime()->currentTimestamp()->comment('Дата и время создания записи');
        $this->column('updated_at')->dateTime()->nullable()->comment('Дата и время обновления записи');
        $this->column('deleted_at')->dateTime()->nullable()->comment('Дата и время перемещения записи в корзину');
    }
    
    /**
     * @param string $column
     * @return Column
     */
    final public function column (string $column): Column
    {
        $column = new Column($column, $this->model);
        $id = spl_object_id($column);
        $this->columns[$id] = $column;
        
        return $this->columns[$id];
    }
}