<?php

namespace CloudCastle\Core\Console\Command\Make;

use CloudCastle\Core\DataBase\Migrate;
use Exception;

final class MakeMigrate extends AbstractMake
{
    public const string DESCRIPTION = 'Сгенерировать новый класс миграции';
    
    protected const string WORK_DIR = APP_ROOT . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrate';
    
    protected const string NAME_SPASE = 'DataBase\\Migrate';
    
    protected const string EXTENDS = Migrate::class;
    
    /**
     * @return string|null
     * @throws Exception
     */
    public function handle (): string|null
    {
        $name = date('Y-m-d_H:i:s') . '_' . class_basename($this->argument('name', default : 'table_create')) . '_' . time();
        $this->setArgument('name', $name);
        $this->setArgument('up_method', $this->getUpMethod($name));
        $this->setArgument('down_method', $this->getDownMethod($name));
        $this->setArgument('up_columns', $this->getUpColumnsMethod($name));
        $this->setArgument('down_columns', $this->getDownColumnsMethod($name));
        $model = $this->argument('model', required : true);
        $args = $this->argument('args', '');
        
        if (str_contains($args, '-m')) {
            $this->run('make:model', ['name' => $model]);
            sleep(2);
        }
        
        $model = "\\App\\Models\\" . $model;
        $this->setArgument('model', class_basename($model));
        $this->setArgument('use_model', "use " . trim($model, '\\'));
        
        if (!class_exists($model)) {
            throw new Exception("Class {$model} not found");
        }
        
        $this->make($this->getMakeInfo());
        
        return null;
    }
    
    /**
     * @param string $name
     * @return string
     */
    private function getUpMethod (string $name): string
    {
        if (str_contains($name, 'alter') || str_contains($name, 'edit')) {
            return 'alter';
        }
        
        return 'query';
    }
    
    /**
     * @param string $name
     * @return string
     */
    private function getDownMethod (string $name): string
    {
        if (str_contains($name, 'alter') || str_contains($name, 'edit')) {
            return 'alter';
        }
        
        return 'query';
    }
    
    /**
     * @param string $name
     * @return string
     */
    private function getUpColumnsMethod (string $name): string
    {
        if (!str_contains($name, 'alter') && !str_contains($name, 'edit')) {
            return '$table->column(\'id\')->autoIncrement()->comment(\'Уникальный идентификатор записи в рамках таблицы\');
            $table->column(\'uuid\')->uuid()->comment(\'Уникальный идентификатор записи в рамках базы данных\');' . PHP_EOL . '
            $table->timestamp();';
        }
        
        return '';
    }
    
    /**
     * @param string $name
     * @return string
     */
    private function getDownColumnsMethod (string $name): string
    {
        if (!str_contains($name, 'alter') && !str_contains($name, 'edit')) {
            return '$table->drop();';
        }
        
        return '';
    }
}