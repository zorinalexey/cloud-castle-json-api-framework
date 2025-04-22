<?php

namespace CloudCastle\Core\Model;

use CloudCastle\Core\DataBase\Builder\Select;
use CloudCastle\Core\Repository\Repository;
use DateTime;
use PDO;

/**
 * @property int|null $id
 * @property string|null $uuid
 * @property DateTime|null $created_at
 * @property DateTime|null $updated_at
 * @property DateTime|null $deleted_at
 *
 * @method getById(int $id)
 * @method getByUuid(string $uuid)
 */
interface ModelInterface
{
    /**
     *
     */
    public const array MAIN_COLUMNS = [
        'id',
        'uuid',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    
    /**
     *
     */
    public const array MAIN_CASTS = [
        'id' => 'int|nullable',
        'uuid' => 'string|nullable',
        'created_at' => 'datetime|nullable',
        'updated_at' => 'datetime|nullable',
        'deleted_at' => 'datetime|nullable',
    ];
    
    /**
     * @return string
     */
    public static function table (): string;
    
    /**
     * @return PDO
     */
    public static function getConnection (): PDO;
    
    /**
     * @param array $data
     * @return static
     */
    public static function make (array $data): static;
    
    /**
     * @return array
     */
    public static function getColumns (): array;
    
    /**
     * @return array
     */
    public static function getCasts (): array;
    
    /**
     * @param array $filters
     * @return Select
     */
    public function getFilter (array $filters): Select;
    
    /**
     * @return void
     */
    public function load (): void;
    
    /**
     * @return Repository
     */
    public function getRepository (): Repository;
}