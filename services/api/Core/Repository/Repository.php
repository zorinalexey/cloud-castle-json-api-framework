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
        return $this->model->getFilter($filters);
    }
    
    /**
     * @param int|null $id
     * @return ModelInterface|null
     */
    final public function getById (int|null $id, mixed ...$filters): ModelInterface|null
    {
        if(!$id){
            return null;
        }
        
        return $this->filter(['id' => $id])->first();
    }
    
    /**
     * @param string $uuid
     * @return ModelInterface
     */
    final public function getByUuid (string|null $uuid, mixed ...$filters): ModelInterface|null
    {
        if(!$uuid){
            return null;
        }
        
        $select = $this->filter(['uuid' => $uuid]);
        
        var_dump((string)$select);
        
        return $select->first();
    }
    
    /**
     * @param array $filters
     * @return Collection
     */
    final public function get (array $filters): Collection
    {
        return $this->filter($filters)->all();
    }
    
    /**
     * @param array $filters
     * @return PaginateCollection
     */
    final public function paginate (array $filters): PaginateCollection
    {
        return $this->filter($filters)->paginate();
    }
    
    /**
     * @param mixed ...$data
     * @return ModelInterface|null
     */
    public function create (mixed ...$data): ModelInterface|null
    {
        return null;
    }
    
    public function update (int|string $id, mixed ...$data): ModelInterface
    {
        $params = [];
        $this->setParams($id, $params);
        $old = $this->filter($params)->first();
        
        return $this->model;
    }
    
    public function softDelete (int|string $id, mixed ...$filters): ModelInterface|null
    {
        $params = [];
        $this->setParams($id, $params);
        $old = $this->filter($params)->first();
        
        return $this->model;
    }
    
    public function hardDelete (int|string $id, mixed ...$filters): ModelInterface
    {   $params = [];
        $this->setParams($id, $params);
        $old = $this->filter($params)->first();
        
        return $this->model;
    }
    
    public function restore (int|string $id, mixed ...$filters): ModelInterface
    {
        $params = [];
        $this->setParams($id, $params);
        $old = $this->filter($params)->first();
        
        return $this->model;
    }
    
    /**
     * @param array $data
     * @return ModelInterface|null
     */
    public function save (mixed ...$data): ModelInterface|null
    {
        if(isset($data['uuid'], $data['id'])){
            return $this->model::make($data)->update();
        }
        
        return $this->model::make($data)->create();
    }
    
    /**
     * @param int|string $id
     * @param array $params
     * @return void
     */
    private function setParams (int|string $id, array &$params): void
    {
        if(is_string($id)){
            $params['uuid'] = $id;
        }else{
            $params['id'] = $id;
        }
    }
    
}