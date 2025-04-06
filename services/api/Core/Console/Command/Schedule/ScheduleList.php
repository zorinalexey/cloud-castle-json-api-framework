<?php

namespace CloudCastle\Core\Console\Command\Schedule;

use CloudCastle\Core\Console\Command\Command;

final class ScheduleList extends Command
{
    public const string DESCRIPTION = 'Просмотр списка задач';
    
    public function handle (): null
    {
        $list = config('schedule');
        $currentTime = '';
        
        foreach ($list as $time => $tasks) {
            if ($currentTime !== $time) {
                echo PHP_EOL;
            }
            
            echo "{$time} " . PHP_EOL;
            
            $this->echoTasks($tasks);
            $currentTime = $time;
        }
        
        return null;
    }
    
    private function echoTasks (array $tasks): void
    {
        foreach ($tasks as $task) {
            $class = config("command.{$task}");
            echo "\t {$task} - " . $class::DESCRIPTION . PHP_EOL;
        }
    }
}