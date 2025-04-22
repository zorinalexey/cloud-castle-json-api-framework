<?php

namespace CloudCastle\Core\Model;

use CloudCastle\Core\DataBase\Builder\Select;
use CloudCastle\Core\Filters\AbstractFilter;
use CloudCastle\Core\Helpers\Str;
use CloudCastle\Core\Repository\Repository;
use CloudCastle\Core\Validator\ValidationException;
use DateTime;
use Exception;
use PDO;
use stdClass;

/**
 * @property int $id
 * @property string $uuid
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property DateTime $deleted_at
 */
class Model extends stdClass implements ModelInterface
{
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
            
            $column = trim($column, '_');
            
            if (in_array($column, $columns)) {
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
        if (static::$table !== null) {
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
            
            static::$config = [
                'dsn' => static::getDsn($dbConf),
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
            
            $dsn .= $dbConf['host'];
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
        $dbConf = $config[static::$dbName] ?? null;
        
        if (!$dbConf) {
            throw new Exception("Database '{$config['options']} configuration is missing", 50111);
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
    private static function setCasts (Modelinterface $model, string $column, mixed $value, string|null $cast)
    {
        $model->{$column} = $value;
        
        if (config('validator')[$cast] ?? null) {
            validated($model->{$column}, $cast);
        }
    }
    
    /**
     * @param string $column
     * @param mixed $value
     * @return void
     */
    private function setRelations (string $column, mixed $value): void
    {
    
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
        
        if (method_exists($this, $name)) {
            $result = $this->{$name}();
            $this->runEvents("{$name}", $result);
            $this->runObservers("{$name}", $result);
            
            return $result;
        }
        
        if (method_exists($this->getRepository(), $name)) {
            $model = $this->getRepository()->{$name}();
            $this->runEvents("after_{$name}", $model);
            $this->runObservers("after_{$name}", $model);
            $model->old = $old;
            
            return $model;
        }
        
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
            $class = str_replace('Model', 'Repository', static::class);
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
    
    private function runObservers (string $string, ModelInterface $old)
    {
    }
}