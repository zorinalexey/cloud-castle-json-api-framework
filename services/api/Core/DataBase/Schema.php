<?php

namespace CloudCastle\Core\DataBase;

use Closure;
use CloudCastle\Core\DataBase\Table\AlterTable;
use CloudCastle\Core\DataBase\Table\CreateTable;
use CloudCastle\Core\Model\Model;
use Exception;

final class Schema
{
    /**
     * @var Model
     */
    private string $model = '';
    
    /**
     * @param string $model
     * @return self
     */
    public static function model (string $model): self
    {
        $obj = new self();
        $obj->model = $model;
        
        return $obj;
    }
    
    /**
     * @param Closure $func
     * @return bool
     * @throws Exception
     */
    public function query (Closure $func): bool
    {
        $table = new CreateTable($this->model);
        $func($table);
        $sql = (string) $table;
        
        return $this->model::getConnection()->exec($sql) !== false;
    }
    
    /**
     * @param Closure $func
     * @return bool
     * @throws Exception
     */
    public function alter (Closure $func): bool
    {
        $table = new AlterTable($this->model);
        $func($table);
        
        return $this->model::getConnection()->exec((string) $table) !== false;
    }
}