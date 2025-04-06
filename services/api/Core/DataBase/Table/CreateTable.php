<?php

namespace CloudCastle\Core\DataBase\Table;

final class CreateTable extends Table
{
    /**
     * @return string
     */
    protected function toSql (): string
    {
        return $this->sql;
    }
}