<?php

namespace CloudCastle\Core\DataBase\Builder;

use CloudCastle\Core\Model\Collection;
use CloudCastle\Core\Model\Model;
use CloudCastle\Core\Model\ModelInterface;
use CloudCastle\Core\Model\PaginateCollection;
use CloudCastle\Core\Router\Router;
use DateTime;
use Generator;
use PDOStatement;
use Stringable;

final class Select implements Stringable
{
    /**
     * @var array
     */
    protected array $binds = [];
    /**
     * @var ModelInterface
     */
    private readonly ModelInterface $model;
    /**
     * @var array
     */
    private array $conditions = [];
    private array $order = [];
    private int $offset = 0;
    private int $limit = 0;
    
    final public function __construct (Modelinterface $model)
    {
        $this->model = $model;
    }
    
    /**
     * @param string $column
     * @param mixed $value
     * @param string $operator
     * @return $this
     */
    final public function where (string $column, mixed $value, string $operator = '='): self
    {
        $bindName = $this->bindName($value, $column);
        $this->whereRaw("{$column} {$operator} {$bindName}");
        
        return $this;
    }
    
    /**
     * @param mixed $value
     * @param int|string|null $name
     * @return string
     */
    final protected function bindName (mixed $value, int|string|null $name = null): string
    {
        if (!$name || is_int($name)) {
            $name = ':bind_' . md5(serialize($value));
        } else {
            if (!str_contains($name, ':')) {
                $name = ":".str_replace(['.', ':'], ['_', ''], $name);
            }
        }
        
        if (is_bool($value)) {
            $value = $value ? 1 : 0;
        }
        
        if ($value instanceof DateTime) {
            $value = $value->format('Y-m-d H:i:s');
        }
        
        $this->binds[$name] = $value;
        
        return $name;
    }
    
    /**
     * @param string $sql
     * @param array $params
     * @param string $prefix
     * @return $this
     */
    final public function whereRaw (string $sql, array $params = [], string $prefix = 'AND'): self
    {
        $this->conditions[] = "{$prefix} {$sql}";
        $this->setBinds($params);
        
        return $this;
    }
    
    /**
     * @param array|string $params
     * @return void
     */
    private function setBinds (array|string $params): void
    {
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                $this->bindName($value, $key);
            }
        }
    }
    
    /**
     * @param string $column
     * @param mixed $value
     * @param string $operator
     * @return $this
     */
    public function whereOr (string $column, mixed $value, string $operator = '='): self
    {
        array_unshift($this->conditions, '((');
        $bindName = $this->bindName($value, $column);
        $this->conditions[] = ')';
        $this->whereRaw("{$column} {$operator} {$bindName}", prefix : 'OR');
        $this->conditions[] = ')';
        
        return $this;
    }
    
    /**
     * @param string $column
     * @param mixed $value
     * @param string $operator
     * @return $this
     */
    public function and (string $column, mixed $value, string $operator = '='): self
    {
        $bindName = $this->bindName($value, $column);
        $this->whereRaw("{$column} {$operator} {$bindName}");
        
        return $this;
    }
    
    /**
     * @param string $column
     * @param mixed $value
     * @param string $operator
     * @return $this
     */
    public function or (string $column, mixed $value, string $operator = '='): self
    {
        $bindName = $this->bindName($value, $column);
        $this->whereRaw("{$column} {$operator} {$bindName}", prefix : 'OR');
        
        return $this;
    }
    
    /**
     * @return Model|null
     */
    public function first (): Model|null
    {
        $connection = $this->model::getConnection();
        $sql = (string) $this;
        $stmt = $connection->prepare($sql);
        $stmt->execute($this->binds);
        
        return $this->setItem($this->point($stmt));
    }
    
    /**
     * @param Generator|null $cursor
     * @return Model|ModelInterface|null
     */
    private function setItem (Generator|null $cursor): Model|ModelInterface|null
    {
        if (!$cursor) {
            return null;
        }
        
        $data = [];
        
        foreach ($cursor as $key => $value) {
            $data[$key] = $value;
        }
        
        return $this->model::make($data);
    }
    
    /**
     * @param PDOStatement|false $stmt
     * @return Generator
     */
    private function point (PDOStatement|false $stmt): Generator
    {
        if ($stmt) {
            while ($row = $stmt->fetch()) {
                yield $row;
            }
        }
    }
    
    /**
     * @return Collection
     */
    public function all (): Collection
    {
        $stmt = $this->model::getConnection()->prepare((string) $this);
        $stmt->execute($this->binds);
        $items = [];
        $this->setItems($items, $stmt);
        
        return Collection::make($items);
    }
    
    /**
     * @param array $items
     * @param false|PDOStatement $stmt
     * @return void
     */
    private function setItems (array &$items, false|PDOStatement $stmt): void
    {
        $items[] = $this->setItem($this->point($stmt));
    }
    
    /**
     * @return PaginateCollection
     */
    public function paginate (): PaginateCollection
    {
        $request = Router::getRequest();
        
        if (!(int) $perPage = $request->per_page) {
            $perPage = 100;
        }
        
        if (!((int) $page = $request->page) || $page < 1) {
            $page = 1;
        }
        
        $limit = $perPage;
        $offset = ($page - 1) * $perPage;
        $sqlTotal = /** @lang text */
            "SELECT COUNT(*) as total FROM ({$this}) as paginate";
        $connection = $this->model::getConnection();
        $stmt = $connection->prepare($sqlTotal);
        $stmt->execute($this->binds);
        $this->limit($limit)->offset($offset);
        $itemsStmt = $connection->prepare((string) $this);
        $itemsStmt->execute($this->binds);
        $items = [];
        $this->setItems($items, $itemsStmt);
        
        $data = [
            'limit' => (int) $limit,
            'offset' => (int) $offset,
            'page' => (int) $page,
            'total' => (int) $stmt->fetch()['total'],
            'per_page' => (int) $perPage,
            'items' => Collection::make($items),
        ];
        
        return PaginateCollection::make($data);
    }
    
    /**
     * @param int $offset
     * @return $this
     */
    public function offset (int $offset): self
    {
        $this->offset = $offset;
        
        return $this;
    }
    
    /**
     * @param int $limit
     * @return $this
     */
    public function limit (int $limit): self
    {
        $this->limit = $limit;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function __toString (): string
    {
        $columns = $this->model::getColumns();
        $table = $this->model::table();
        $fields = [];
        $this->setFields($fields, $table, $columns);
        $sql = "SELECT\n\t" . implode(",\n\t", $fields) . "\nFROM\n\t{$table}\n";
        
        
        if($this->conditions){
            $sql .= "WHERE\n\t" . preg_replace('~^(?:AND|OR)\s+|(?:AND|OR)\s*$~iu', '', trim(implode("\n\t", $this->conditions)))."\n";
        }
        
        if ($this->order) {
            $sql .= "ORDER BY\n\t" . implode(",\n\t", $this->order) . "\n";
        }
        
        if ($this->limit) {
            $sql .= "LIMIT {$this->limit}\n";
        }
        
        if ($this->offset) {
            $sql .= "OFFSET {$this->offset}\n";
        }
        
        return $sql;
    }
    
    /**
     * @param array $fields
     * @param string $table
     * @param array $columns
     * @return void
     */
    private function setFields (array &$fields, string $table, array $columns): void
    {
        foreach ($columns as $column) {
            $fields[] = "{$table}.{$column} AS {$table}_{$column}";
        }
    }
    
    /**
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy (string $column, string $direction = 'ASC'): self
    {
        $direction = mb_strtoupper($direction);
        
        if ($direction !== 'ASC') {
            $direction = 'DESC';
        }
        
        $this->order[] = "{$column} {$direction}";
        
        return $this;
    }
    
    /**
     * @return Generator|null
     */
    public function cursor (): Generator|null
    {
        $stmt = $this->model::getConnection()->prepare((string) $this);
        
        if ($stmt->execute($this->binds)) {
            while ($row = $stmt->fetch()) {
                yield $this->model::make($row);
            }
        }
        
        return null;
    }
}