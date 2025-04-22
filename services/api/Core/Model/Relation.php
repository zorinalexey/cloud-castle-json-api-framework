<?php

namespace CloudCastle\Core\Model;

final class Relation
{
    
    private array $relations = [
    
    ];
    private readonly Model $model;
    
    public function __construct (Model $model)
    {
        $this->model = $model;
    }
    
    /**
     * @param ModelInterface $related
     * @param string|null $foreignKey
     * @param string|null $localKey
     * @return array
     */
    public function hasOne (ModelInterface $related, string|null $foreignKey = null, string|null $localKey = null): array
    {
        $i = count($this->relations[]);
        $this->relations[][$i] = [
            'related' => $related,
            'model' => $this->model,
            'method' => 'first',
            'type' => RelationType::HAS_ONE,
            'params' => [
                $this->setForeignKey($foreignKey) => $this->model->{$this->setLocalKey($related, $localKey)}
            ],
        ];
        
        return $this->relations['has_one'][$i];
    }
    
    /**
     * @param string|null $foreignKey
     * @return string
     */
    private function setForeignKey (string|null $foreignKey): string
    {
        if (!$foreignKey) {
            return 'uuid';
        }
        
        return $foreignKey;
    }
    
    /**
     * @param ModelInterface $related
     * @param string|null $localKey
     * @return string
     */
    private function setLocalKey (ModelInterface $related, string|null $localKey): string
    {
        if (!$localKey) {
            return "{$related::table()}_uuid";
        }
        
        return $localKey;
    }
    
    /**
     * @param ModelInterface $related
     * @param string|null $foreignKey
     * @param string|null $localKey
     * @return array
     */
    public function hasMany (ModelInterface $related, string|null $foreignKey = null, string|null $localKey = null): array
    {
        $i = count($this->relations[]);
        $this->relations[$i] = [
            'related' => $related,
            'model' => $this->model,
            'method' => 'all',
            'type' => RelationType::HAS_MANY,
            'params' => [
                $this->setForeignKey($foreignKey) => $this->model->{$this->setLocalKey($related, $localKey)}
            ],
        ];
        
        return $this->relations[$i];
    }
}