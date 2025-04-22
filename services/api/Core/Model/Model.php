<?php

namespace CloudCastle\Core\Model;

use CloudCastle\Core\DataBase\Builder\Select;
use CloudCastle\Core\Filters\AbstractFilter;
use CloudCastle\Core\Helpers\Str;
use CloudCastle\Core\Repository\Repository;
use CloudCastle\Core\Validator\ValidationException;
use Exception;
use PDO;
use stdClass;

/**
 * @method static Model|null save(array $data = [])
 * @method static Model|null create(array $data = [])
 * @method static Model|null update(string|int $id, array $data = [])
 * @method static Model|null softDelete(string|int $id)
 * @method static Model|null hardDelete(string|int $id)
 * @method static Model|null restore(string|int $id)
 */
abstract class Model extends stdClass implements ModelInterface
{
    /**
     * @var ModelInterface|null
     */
    public ModelInterface|null $old = null;
    
    /**
     * @var string|null
     */
    protected static string|null $table = null;
    
    /**
     * @var PDO|null
     */
    private static PDO|null $connection = null;
    
    /**
     * @var array
     */
    private static array $config = [];
    
    /**
     * @var string
     */
    protected static string $dbName = 'default';
    
    /**
     * @var array
     */
    protected static array $columns = [
    
    ];
    
    /**
     * @var array
     */
    protected static array $casts = [
    
    ];
    
    /**
     * @var array
     */
    protected static array $filter = [];
    
    /**
     * @var array
     */
    protected static array $repository = [];
    
    /**
     * @var Relation|null
     */
    protected static Relation|null $relation = null;
    
    /**
     * @return array
     */
    final public static function getColumns (): array
    {
        return [
            ...static::$columns,
            ...self::MAIN_COLUMNS,
        ];
    }
    
    /**
     * @param array $data
     * @return static
     * @throws ValidationException
     */
    final public static function make (array $data): static
    {
        $columns = static::getColumns();
        $pattern = '~^' . static::table() . '_(\w+)$~ui';
        $model = new static();
        $casts = $model->getCasts();
        
        foreach ($data as $column => $value) {
            if (preg_match($pattern, $column)) {
                $column = preg_replace($pattern, '$1', $column);
            }
            
            $column = trim(trim($column), '_');
            
            if (in_array($column, $columns) || property_exists($model, $column)) {
                $model->{$column} = $value;
                static::setCasts($model, $column, $value, $casts[$column] ?? null);
            } else {
                $model->setRelations($column, $value);
            }
        }
        
        return $model;
    }
    
    /**
     * @return PDO
     * @throws Exception
     */
    final public static function getConnection (): PDO
    {
        if (!static::$connection) {
            static::$connection = new PDO(...static::getConfig());
        }
        
        return static::$connection;
    }
    
    /**
     * @return string
     */
    final public static function table (): string
    {
        if (!static::$table) {
            static::$table = Str::toSnakeCase(class_basename(static::class));
        }
        
        return static::$table;
    }
    
    /**
     * @return array
     * @throws Exception
     */
    final protected static function getConfig (): array
    {
        if (!static::$config) {
            $config = config('database');
            $dbConf = static::getDbConf($config);
            $dsn = static::getDsn($config);
            
            static::$config = [
                'dsn' => $dsn,
                'username' => $dbConf['username'],
                'password' => $dbConf['password'],
                'options' => [...$config['options'], ...$dbConf['options'] ?? []],
            ];
        }
        
        return static::$config;
    }
    
    /**
     * @param string $driver
     * @return string
     */
    final protected static function setCharset (string $driver): string
    {
        return (string) match ($driver) {
            'mysql' => ';charset=utf8',
            'pgsql' => ";options='--client_encoding=UTF8'",
            'sqlsrv' => ';CharacterSet=UTF-8',
            'sqlite' => '?encoding=UTF-8',
            default => null
        };
    }
    
    /**
     * @param array $config
     * @return string
     * @throws Exception
     */
    final protected static function getDsn (array $config)
    {
        $dbConf = static::getDbConf($config);
        $dsn = "{$dbConf['driver']}:";
        
        if (isset($dbConf['file'])) {
            $dsn .= $dbConf['host'];
        } else {
            static::checkConf($dbConf);
            
            $dsn .= "host={$dbConf['host']}";
            $dsn .= ";dbname={$dbConf['database']}";
            $dsn .= ";port={$dbConf['port']}";
        }
        
        $dsn .= static::setCharset($dbConf['driver']);
        
        return $dsn;
    }
    
    /**
     * @param array $config
     * @return array
     * @throws Exception
     */
    final protected static function getDbConf (array $config): array
    {
        $dbConf = $config['connections'][static::$dbName] ?? null;
        
        if (!$dbConf) {
            throw new Exception("Database '".static::$dbName."' configuration is missing", 50111);
        }
        
        return $dbConf;
    }
    
    /**
     * @param array $dbConf
     * @return void
     * @throws Exception
     */
    final protected static function checkConf (array $dbConf): void
    {
        if (!isset($dbConf['host'])) {
            throw new Exception('Database hostname not set', 50112);
        }
        
        if (!isset($dbConf['database'])) {
            throw new Exception('Database not set', 50113);
        }
        
        if (!isset($dbConf['port'])) {
            throw new Exception('Database port not set', 50114);
        }
    }
    
    /**
     * @param Modelinterface $model
     * @param string $column
     * @param mixed $value
     * @param array|null $casts
     * @return void
     * @throws ValidationException
     */
    private static function setCasts (Modelinterface $model, string $column, mixed $value, string|null $casts): void
    {
        foreach(explode('|', $casts) as $cast) {
            if (config('validator')[$cast] ?? null) {
                validated($model->{$column}, $cast);
            }
        }
    }
    
    /**
     * @param string $column
     * @param mixed $value
     * @return void
     * @throws ValidationException
     */
    private function setRelations (string $column, mixed $value): void
    {
        foreach ($this->relations($this->getRelation()) as $entity => $relation) {
            $pattern = "~^(?<table>{$relation['table']})_(?<column>\w+)$~ui";
            $table = null;
            
            if(preg_match($pattern, $column, $match)) {
                $column = $match['column'];
                $table = $match['table'];
                
                if(!$this->{$entity} && $relation['table'] === $match['table']) {
                    $this->{$entity} = new $relation['related']();
                }
            }
            
            if($table && $this->{$entity} && $relation['table'] === $match['table']){
                $this->setCasts($this->{$entity}, $column, $value, $this->{$entity}->getCasts()[$column] ?? null);
            }
        }
    }
    
    /**
     * @param array $relations
     * @return void
     * @throws Exception
     */
    final public function load (array $relations = []): void
    {
        $relation = $this->getRelation();
        
        foreach ($this->relations($relation) as $entity => $relation) {
            if ($relations) {
                if (in_array($entity, $relations)) {
                    $this->setRelation($entity, $relation);
                } else {
                    throw new Exception("Relation '{$entity}' not exists", 50115);
                }
            } else {
                $this->setRelation($entity, $relation);
            }
        }
    }
    
    /**
     * @param string $name
     * @return mixed
     */
    public function __get (string $name): mixed
    {
        $old = $this->getRepository()->getByUuid($this->uuid);
        $this->runEvents("before_{$name}", $old);
        $this->runObservers("before_{$name}", $old);
        
        try {
            $this->load([$name]);
            $model = $this->{$name};
            $this->runEvents("after_{$name}", $model);
            $this->runObservers("after_{$name}", $model);
            
            return $model;
        } catch (Exception $e) {
        
        }
        
        return null;
    }
    
    public function __call(string $name, array $arguments): mixed
    {
        $old = null; // $this->getRepository()->getByUuid($this->uuid);
        $this->runEvents("before_{$name}", $old);
        $this->runObservers("before_{$name}", $old);
        
        if (method_exists($this->getRepository(), $name)) {
            $params = [...$this->toArray()];
            
            if($old){
                $params['old'] = $old;
            }
            
            $model = $this->getRepository()->{$name}(...$params);
            $this->runEvents("after_{$name}", $model);
            $this->runObservers("after_{$name}", $model);
            
            return $model;
        }
        
        return null;
    }
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return objToArray($this);
    }
    
    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        $obj = static::make($arguments[0]);
        
        return $obj->{$name}(...$arguments);
    }
    
    /**
     * @param Relation $relation
     * @return array
     */
    protected function relations (Relation $relation): array
    {
        return [
        
        ];
    }
    
    /**
     * @param string $entity
     * @param array $relation
     * @return void
     */
    private function setRelation (string $entity, array $relation): void
    {
        if (!$this->{$entity}) {
            $model = $relation['related'];
            $data = $relation['params'];
            $method = $relation['method'];
            /**
             * @var ModelInterface $model
             * @var array $data
             * @var string $method
             */
            $this->{$entity} = $model::getRepository()->filter($data)->{$method}();
        }
    }
    
    /**
     * @return Repository
     */
    final public function getRepository (): Repository
    {
        if (!isset(static::$repository[$this->uuid])) {
            /** @var Repository $class */
            $class = str_replace('Models', 'Repository', static::class);
            static::$repository[$this->uuid] = new $class($this);
        }
        
        return static::$repository[$this->uuid];
    }
    
    /**
     * @param array $filters
     * @return Select
     */
    public function getFilter (array $filters): Select
    {
        if (!isset(static::$filter[$this->uuid])) {
            /** @var AbstractFilter $class */
            $class = str_replace('Model', 'Filter', static::class);
            static::$filter[$this->uuid] = $class::apply($this, $filters);
        }
        
        return static::$filter[$this->uuid];
    }
    
    /**
     * @return array
     */
    public static function getCasts (): array
    {
        return [
            ...static::$casts,
            ...static::MAIN_CASTS,
        ];
    }
    
    /**
     * @return Relation
     */
    private function getRelation (): Relation
    {
        if(!static::$relation) {
            static::$relation = new Relation($this);
        }
        
        return static::$relation;
    }
    
    private function runEvents (string $string, $model)
    {
    }
    
    private function runObservers (string $string, ModelInterface|null $model)
    {
    }
}