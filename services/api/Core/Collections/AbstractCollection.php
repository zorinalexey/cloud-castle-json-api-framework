<?php

namespace CloudCastle\Core\Collections;

use ArrayAccess;
use ArrayIterator;
use Closure;
use CloudCastle\Core\Traits\StringableTrait;
use Countable;
use Exception;
use Iterator;
use Serializable;
use stdClass;
use Stringable;
use Traversable;

abstract class AbstractCollection extends stdClass implements Traversable, Iterator, Countable, ArrayAccess, Serializable, Stringable
{
    use StringableTrait;
    
    /**
     * @var array
     */
    protected array $items;
    
    /**
     * @var int
     */
    private int $position = 0;
    
    /**
     * @param array $items
     * @return static
     */
    abstract public static function make(array $items = []): static;
    
    /**
     * @param array $items
     */
    final public function __construct(array $items = [])
    {
        $this->items = [...$items];
    }
    
    /**
     * @param mixed $item
     * @return $this
     */
    final public function add(mixed $item): static
    {
        $this->items[] = $item;
        
        return $this;
    }
    
    /**
     * @param int $key
     * @return $this
     */
    final public function remove(int $key): static
    {
        if($this->exists($key)) {
            unset($this->items[$key]);
        }
        
        return $this;
    }
    
    /**
     * @return array
     */
    final public function toArray(): array
    {
        return $this->values();
    }
    
    /**
     * @return mixed
     */
    final public function getPrev(): mixed
    {
        return $this->items[key($this->items) - 1]?? null;
    }
    
    /**
     * @return mixed
     */
    final public function getNext(): mixed
    {
        return $this->items[key($this->items) + 1]?? null;
    }
    
    /**
     * Возвращает индекс текущей позиции коллекции.
     * @return int
     */
    final public function key(): int
    {
        $this->position = key($this->items);
        
        return $this->position;
    }
    
    /**
     * @return void
     */
    final public function rewind(): void
    {
        reset($this->items);
    }
    
    /**
     * @return bool
     */
    final public function valid(): bool
    {
        return $this->exists(key($this->items));
    }
    
    /**
     * @return Traversable
     */
    final public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
    
    /**
     * @param $offset
     * @return bool
     */
    final public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }
    
    /**
     * @param $offset
     * @return void
     */
    final public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }
    
    /**
     * @param $offset
     * @return mixed|null
     */
    final public function offsetGet($offset): mixed
    {
        return $this->offsetExists($offset) ? $this->items[$offset] : null;
    }
    
    /**
     * @param $offset
     * @param $value
     * @return void
     */
    final public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }
    
    /**
     * @return string
     */
    final public function serialize(): string
    {
        return serialize($this->items);
    }
    
    /**
     * @param mixed $data
     * @return void
     */
    final public function unserialize(mixed $data): void
    {
        $this->items = unserialize($data);
    }
    
    /**
     * @param callable|Closure $callback
     * @return void
     */
    final public function each(callable|Closure $callback): void
    {
        foreach ($this->items as $key => $item) {
            $callback($item, $key);
        }
    }
    
    /**
     * @return array[]
     */
    final public function __serialize(): array
    {
        return ['items' => $this->items];
    }
    
    /**
     * @param array $data
     * @return void
     * @throws Exception
     */
    final public function __unserialize(array $data): void
    {
        if(!isset($data['items'])) {
            throw new Exception ('Items must be an array');
        }
        
        $this->items = $data['items'];
        $this->position = key($this->items);
    }
    
    /**
     * @param callable|Closure $callback
     * @return static
     */
    final public function groupBy(callable|Closure $callback): static
    {
        $grouped = [];
        
        foreach ($this->items as $item) {
            $key = $callback($item);
            $grouped[$key][] = $item;
        }
        
        return static::make($grouped);
    }
    
    /**
     * @param mixed $item
     * @return bool
     */
    final public function contains(mixed $item): bool
    {
        return in_array($item, $this->items, true);
    }
    
    /**
     * @return static
     */
    final public function flatten(): static
    {
        $flattened = [];
        array_walk_recursive($this->items, function($item) use (&$flattened) {
            $flattened[] = $item;
        });
        
        return static::make($flattened);
    }
    
    /**
     * Проверяет, каждый ли элемент коллекции удовлетворяет callback-функции
     * @param callable|Closure $callback
     * @return bool
     */
    final public function all(callable|Closure $callback): bool
    {
        return array_all($this->items, $callback);
    }
    
    /**
     * Проверяет, содержит ли коллекция хотя бы один элемент, который удовлетворяет callback-функции
     * @param callable|Closure $callback
     * @return bool
     */
    final public function any(callable|Closure $callback): bool
    {
        return array_any($this->items, $callback);
    }
    
    /**
     * Разбивает коллекцию на части
     * @param int $length
     * @param bool $preserve_keys
     * @return static
     */
    final public function chunk(int $length, bool $preserve_keys = false): static
    {
        $chunks = array_chunk($this->items, $length, $preserve_keys);
        
        foreach ($chunks as $key => $chunk) {
            $chunks[$key] = static::make($chunk);
        }
        
        return static::make($chunks);
    }
    
    /**
     * Возвращает коллекцию из значений одного столбца входного массива
     * @param int|string|null $column_key
     * @param int|string|null $index_key
     * @return $this
     */
    final public function column(int|string|null $column_key, int|string|null $index_key = null): static
    {
        return static::make(array_column($this->items, $column_key, $index_key));
    }
    
    /**
     * Подсчитывает количество вхождений каждого отдельного значения в коллекции
     * @return $this
     */
    final public function countValues(): static
    {
        return static::make(array_count_values($this->items));
    }
    
    /**
     * Вычисляет расхождение в коллекциях, без учета ключей
     * @param AbstractCollection $collection
     * @return $this
     */
    final public function diff(self $collection): static
    {
        return static::make(array_diff($this->items,  $collection->toArray()));
    }
    
    /**
     * Вычисляет расхождение в коллекциях с учетом ключей
     * @param AbstractCollection $collection
     * @return $this
     */
    final public function diffAssoc(self $collection): static
    {
        if($collection instanceof self) {
            $collection = $collection->toArray();
        }
        
        return static::make(array_diff_assoc($this->items, $collection));
    }
    
    /**
     * Фильтрует элементы коллекции через callback-функцию
     * @param callable|Closure|null $callback
     * @param int $mode
     * @return $this
     */
    final public function filter(callable|Closure|null $callback = null, int $mode = 0): static
    {
        return static::make(array_filter($this->items, $callback, $mode));
    }
    
    /**
     * Возвращает первый элемент, который удовлетворяет callback-функции
     * @param callable|Closure $callback
     * @return mixed
     */
    final public function find(callable|Closure $callback): mixed
    {
        return array_find($this->items, fn ($item) => $callback($item));
        
    }
    
    /**
     * Возвращает ключ первого элемента, который удовлетворяет callback-функции
     * @param callable $callback
     * @return int|null
     */
    final public function  findKey(callable $callback): int|null
    {
        return array_find_key($this->items, fn ($item) => $callback($item));
        
    }
    
    /**
     * Вычисляет пересечение коллекции с массивом
     * @param array ...$arrays
     * @return $this
     */
    final public function intersect(array ...$arrays): static
    {
        return static::make(array_intersect($this->items, $arrays));
    }
    
    /**
     * Проверяет, является ли данная коллекция списком
     * @return bool
     */
    final public function isList(): bool
    {
        return array_is_list($this->items);
    }
    
    /**
     * Проверяет, существует ли в коллекции заданный ключ или индекс
     * @param string|int|float|bool|null $key
     * @return bool
     */
    final public function keyExists(string|int|float|bool|null $key): bool
    {
        return array_key_exists($key, $this->items);
    }
    
    /**
     * Получает первый элемент коллекции
     * @return mixed
     */
    final public function first(): mixed
    {
        return $this->items[$this->firstKey()]?? null;
    }
    
    /**
     * Получает последний элемент коллекции
     * @return mixed
     */
    final public function last(): mixed
    {
        return $this->end();
    }
    
    /**
     * Применяет callback-функцию к элементам коллекции
     * @param callable|Closure $callback
     * @param array ...$arrays
     * @return $this
     */
    final public function map(callable|Closure $callback,): static
    {
        return static::make(array_map($callback, $this->items));
    }
    
    /**
     * Сливает одну или несколько коллекций
     * @param array|AbstractCollection ...$arrays
     * @return $this
     */
    final public function merge(self ...$collections): static
    {
        $data = [];
        
        foreach ($collections as $item) {
            $data = [...$data, ...$item->toArray()];
        }
        
        $data = array_unique($data, SORT_REGULAR);
        
        return static::make(array_merge($this->items, ...$data));
    }
    
    /**
     * Дополняет коллекцию значением до заданной длины
     * @param int $length
     * @param mixed $value
     * @return $this
     */
    final public function pad(int $length, mixed $value): static
    {
        return static::make(array_pad($this->items, $length, $value));
    }
    
    /**
     * Извлекает последний элемент коллекции
     * @return mixed
     */
    final public function pop(): mixed
    {
        return array_pop($this->items);
    }
    
    /**
     * Вычисляет произведение значений коллекции
     * @return int|float|null
     */
    final public function product():int|float|null
    {
        return array_product($this->items);
    }
    
    /**
     * Добавляет один или несколько элементов в конец коллекции
     * @param mixed ...$items
     * @return $this
     */
    final public function push(mixed ...$items): static
    {
        array_push($this->items, ...$items);
        
        return $this;
    }
    
    /**
     * Выбирает один или несколько случайных ключей из коллекции
     * @param int $num
     * @return int|string|array
     */
    final public function rand(int $num = 1): int|string|array
    {
        return array_rand($this->items, $num);
    }
    
    /**
     * Итеративно уменьшает коллекцию к единственному значению через callback-функцию
     * @param callable $callback
     * @param mixed|null $initial
     * @return mixed
     */
    final public function reduce(callable|Closure $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->items, $callback, $initial);
    }
    
    /**
     * Заменяет элементы коллекции элементами других коллекций
     * @param AbstractCollection ...$collections
     * @return $this
     */
    final public function replace(self ...$collections): static
    {
        $data = [];
        
        foreach ($collections as $item) {
            $data = [...$data, ...$item->toArray()];
        }
        
        $data = array_unique($data, SORT_REGULAR);
        
        return static::make(array_replace($this->items, ...$data));
    }
    
    /**
     * Рекурсивно заменяет элементы коллекции элементами других коллекций
     * @param AbstractCollection ...$collections
     * @return $this
     */
    final public function replaceRecursive(self ...$collections): static
    {
        $data = [];
        
        foreach ($collections as $item) {
            $data = [...$data, ...$item->toArray()];
        }
        
        $data = array_unique($data, SORT_REGULAR);
        
        return static::make(array_replace_recursive($this->items, ...$data));
    }
    
    /**
     * Возвращает коллекцию с элементами в обратном порядке
     * @param bool $preserve_keys
     * @return $this
     */
    final public function reverse(bool $preserve_keys = false): static
    {
        return static::make(array_reverse($this->items, $preserve_keys));
    }
    
    /**
     * Ищет значение в коллекции, и если находит, возвращает ключ первого найденного элемента
     * @param mixed $needle
     * @param bool $strict
     * @return int|string|false
     */
    final public function search(mixed $needle, bool $strict = false): int|string|false
    {
        return array_search($needle, $this->items, $strict);
    }
    
    /**
     * Извлекает первый элемент коллекции
     * @return mixed
     */
    final public function shift(): mixed
    {
        return array_shift($this->items);
    }
    
    /**
     * Выбирает срез коллекции
     * @param int $offset
     * @param int|null $length
     * @param bool $preserve_keys
     * @return $this
     */
    final public function slice(int $offset, int|null $length = null, bool $preserve_keys = false): static
    {
        return static::make(array_slice($this->items, $offset, $length, $preserve_keys));
    }
    
    /**
     * Удаляет часть коллекции и заменяет её новыми элементами
     * @param int $offset
     * @param int|null $length
     * @param mixed $replacement
     * @return $this
     */
    final public function splice(int $offset, int|null $length = null, mixed $replacement = []): static
    {
        return static::make(array_splice($this->items, $offset, $length, $replacement));
    }
    
    /**
     * Вычисляет сумму значений коллекции
     * @return int|float
     */
    final public function sum(): int|float
    {
        return array_sum($this->items);
    }
    
    /**
     * Вычисляет расхождение коллекций, используя для сравнения callback-функцию
     * @param callable|Closure $closure
     * @param AbstractCollection ...$collection
     * @return $this
     */
    final public function uDiff(callable|Closure $closure, self ...$collection): static
    {
        $data = [];
        
        foreach ($collection as $item) {
            $data = [...$data, ...$item->toArray()];
        }
        
        $data = array_unique($data, SORT_REGULAR);
        
        return static::make(array_udiff($this->items, ...$data, data_compare_func: $closure));
    }
    
    /**
     * Вычисляет пересечение коллекций, используя для сравнения значений callback-функцию
     * @param callable|Closure $closure
     * @param AbstractCollection ...$collection
     * @return $this
     */
    final public function uIntersect(callable|Closure $closure, self ...$collection): static
    {
        $data = [];
        
        foreach ($collection as $item) {
            $data = [...$data, ...$item->toArray()];
        }
        
        $data = array_unique($data, SORT_REGULAR);
        
        return static::make(array_uintersect($this->items, ...$data, data_compare_func: $closure));
    }
    
    /**
     * Удаляет повторяющиеся значения из коллекции
     * @param int $flags
     * @return $this
     */
    final public function unique(int $flags = SORT_STRING): static
    {
        return static::make(array_unique($this->items, $flags));
    }
    
    /**
     * Добавляет один или несколько элементов в начало коллекции
     * @param mixed ...$values
     * @return int
     */
    final public function unShift(mixed ...$values): int
    {
        return array_unshift($this->items, ...$values);
    }
    
    /**
     * Возвращает значения коллекции
     * @return array
     */
    final public function values(): array
    {
        return array_values($this->items);
    }
    
    /**
     * Применяет пользовательскую функцию к каждому элементу коллекции
     * @param callable|Closure $callback
     * @param mixed|null $arg
     * @return true
     */
    final public function walk(callable|Closure $callback, mixed $arg = null): true
    {
        return array_walk($this->items, $callback, $arg);
    }
    
    /**
     * Рекурсивно применяет пользовательскую функцию к каждому элементу коллекции
     * @param callable|Closure $callback
     * @param mixed|null $arg
     * @return true
     */
    final public function walkRecursive(callable|Closure $callback, mixed $arg = null): true
    {
        return array_walk_recursive($this->items, $callback, $arg);
    }
    
    /**
     * Сортирует коллекцию
     * @param callable|Closure|null $callback
     * @return $this
     */
    final public function sort(callable|Closure|null $callback = null): static
    {
        $items = $this->items;
        if ($callback) {
            usort($items, $callback);
        } else {
            sort($items);
        }
        
        return static::make($items);
    }
    
    /**
     * Подсчитывает количество элементов в коллекции
     * @return int
     */
    final public function count(): int
    {
        return count($this->items);
    }
    
    /**
     * Возвращает текущий элемент коллекции
     * @return mixed
     */
    final public function current(): mixed
    {
        $data = current($this->items);
        $this->position = key($this->items);
        
        return $data;
    }
    
    /**
     * Устанавливает внутренний указатель коллекции на последний элемент
     * @return mixed
     */
    final public function end(): mixed
    {
        $data = end($this->items);
        $this->position = key($this->items);
        
        return $data;
    }
    
    /**
     * Проверяет, существует ли значение в коллекции
     * @param mixed $needle
     * @param bool $strict
     * @return bool
     */
    final public function in(mixed $needle, bool $strict = false): bool
    {
        return in_array($needle, $this->items, $strict);
    }
    
    /**
     * Проверяет, существует ли в коллекции заданный ключ или индекс
     * @param int $key
     * @return bool
     */
    final public function exists(int $key): bool
    {
        return isset($this->items[$key]);
    }
    
    /**
     * Получает первый ключ коллекции
     * @return int|string|null
     */
    final public function firstKey(): int|string|null
    {
        return array_key_first($this->items);
    }
    
    /**
     * Получает последний ключ коллекции
     * @return int|string|null
     */
    final public function lastKey(): int|string|null
    {
        return array_key_last($this->items);
    }
    
    /**
     * Возвращает все или некоторое подмножество ключей массива
     * @param mixed|null $filter_value
     * @param bool $strict
     * @return $this
     */
    final public function keys(mixed $filter_value = null, bool $strict = false): static
    {
        if(!$filter_value) {
            $collection = array_keys($this->items);
        }else{
            $collection = array_keys($this->items, $filter_value, $strict);
        }
        
        return static::make($collection);
    }
    
    /**
     * Сдвигает внутренний указатель коллекции на одну позицию назад
     * @return mixed
     */
    final public function prev(): mixed
    {
        $data = prev($this->items);
        $this->position = key($this->items);
        
        return $data;
    }
    
    /**
     * Сдвигает внутренний указатель коллекции на одну позицию вперед
     * @return mixed
     */
    final public function next(): void
    {
        next($this->items);
    }
    
    /**
     * @param int $count
     * @return $this
     */
    final public function take(int $count): static
    {
        return static::make(array_slice($this->items, 0, $count));
    }
    
    /**
     * @param int $count
     * @return $this
     */
    final public function skip(int $count): static
    {
        return static::make(array_slice($this->items, $count));
    }
    
    /**
     * @return $this
     */
    final public function distinct(): static
    {
        return static::make(array_unique($this->items, SORT_REGULAR));
    }
    
    /**
     * @param callable|Closure $callback
     * @return mixed
     */
    final public function findLast(callable|Closure $callback): mixed
    {
        foreach (array_reverse($this->items) as $item) {
            if ($callback($item)) {
                return $item;
            }
        }
        return null;
    }
    
    /**
     * @return string
     */
    final public function toJson(): string
    {
        return json_encode($this->items, JSON_PRETTY_PRINT);
    }
}