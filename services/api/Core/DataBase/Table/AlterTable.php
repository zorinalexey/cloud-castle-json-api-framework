<?php

namespace CloudCastle\Core\DataBase\Table;

final class AlterTable extends Table
{
    /**
     * @return string
     */
    protected function toSql (): string
    {
        return $this->sql;
    }
}