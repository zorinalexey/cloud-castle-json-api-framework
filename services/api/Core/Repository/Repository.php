<?php

namespace CloudCastle\Core\Repository;

use CloudCastle\Core\DataBase\Builder\Select;
use CloudCastle\Core\Model\Collection;
use CloudCastle\Core\Model\ModelInterface;
use CloudCastle\Core\Model\PaginateCollection;

abstract class Repository
{
    protected readonly ModelInterface $model;
    
    public function __construct (ModelInterface $model)
    {
        $this->model = $model;
    }
    
    /**
     * @param array $filters
     * @return Select
     */
    final public function filter (array $filters): Select
    {
        return $this->model::getFilter($filters);
    }
    
    /**
     * @param int $id
     * @return ModelInterface
     */
    final public function getById (int $id): ModelInterface
    {
        return $this->filter(['id' => $id])->first();
    }
    
    /**
     * @param string $uuid
     * @return ModelInterface
     */
    final public function getByUuid (string $uuid): ModelInterface
    {
        return $this->filter(['uuid' => $uuid])->first();
    }
    
    /**
     * @param array $filters
     * @return Collection
     */
    final public function get (array $filters = []): Collection
    {
        return $this->filter($filters)->all();
    }
    
    /**
     * @param array $filters
     * @return PaginateCollection
     */
    final public function paginate (array $filters = []): PaginateCollection
    {
        return $this->filter($filters)->paginate();
    }
    
    public function create (array $data): ModelInterface|null
    {
        
        return null;
    }
    
    public function update (int $id, array $data): ModelInterface
    {
        return $this->model;
    }
    
    public function softDelete (int $id): ModelInterface|null
    {
        
        return $this->model;
    }
    
    public function hardDelete (int $id): ModelInterface
    {
        return $this->model;
    }
    
    public function restore (int $id): ModelInterface
    {
        return $this->model;
    }
}