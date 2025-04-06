<?php

namespace CloudCastle\Core\Console\Command\Schedule;

use CloudCastle\Core\Console\Command\Command;
use Exception;
use InvalidArgumentException;

final class ScheduleRun extends Command
{
    public const string DESCRIPTION = 'Запустить планировщик задач';
    
    private array $currentTime = [];
    
    /**
     * @throws Exception
     */
    public function handle (): null
    {
        $this->currentTime = [
            'min' => (int) date('i'),
            'hours' => (int) date('H'),
            'weekDay' => (int) date('N'),
            'month' => (int) date('m'),
            'year' => (int) date('Y'),
        ];
        
        foreach (config('schedule') as $time => $tasks) {
            if ($this->checkTime($time)) {
                foreach ($tasks as $task) {
                    echo "Task '{$task}' => " . __METHOD__ . " scheduled for {$time}" . PHP_EOL;
                    $this->runTask($task);
                }
            }
        }
        
        return null;
    }
    
    private function checkTime (int|string $time): bool
    {
        [$min, $hours, $weekDay, $month, $year] = explode(' ', $time);
        $checks['min'] = $this->checkArg(trim($min), 'min', 59, 0);
        $checks['hours'] = $this->checkArg(trim($hours), 'hours', 24, 0);
        $checks['weekDay'] = $this->checkArg(trim($weekDay), 'weekDay', 7, 1);
        $checks['month'] = $this->checkArg(trim($month), 'month', 12, 1);
        $checks['year'] = $this->checkArg(trim($year), 'year');
        
        return !in_array(false, $checks, true);
    }
    
    /**
     * @param string $arg
     * @param string $varName
     * @param int|null $max
     * @param int|null $min
     * @return bool
     */
    private function checkArg (string $arg, string $varName, int|null $max = null, int|null $min = null): bool
    {
        if ($arg === '*') {
            return true;
        }
        
        if (preg_match('~^(?<start>\d{1,2})-(?<end>\d{1,2})$~', $arg, $matches)) {
            $start = (int) $matches['start'];
            $end = (int) $matches['end'];
            $this->checkMinMax($start, $varName, $min, $max);
            $this->checkMinMax($end, $varName, $min, $max);
            $this->checkStartEnd($start, $end);
            
            if ($start >= $this->currentTime[$varName] && $end <= $this->currentTime[$varName]) {
                return true;
            }
        }
        
        if (preg_match('~^(?<current>\d{1,2})$~', $arg, $matches)) {
            if ((int) $matches['current'] === $this->currentTime[$varName]) {
                $this->checkMinMax((int) $matches['current'], $varName, $min, $max);
                
                return true;
            }
        }
        
        if (preg_match('~^(?<start>(\d{1,2}|\*))/(?<end>\d{1,2})$~', $arg, $matches)) {
            $start = ($matches['start'] === '*') ? $this->currentTime[$varName] : (int) $matches['start'];
            $end = (int) $matches['end'];
            $this->checkMinMax($start, $varName, $min, $max);
            $this->checkMinMax($end, $varName, $min, $max);
            $this->checkStartEnd($start, $end);
            
            if ($start % $end === 0) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * @param int $value
     * @param string $varName
     * @param int|null $min
     * @param int|null $max
     * @return void
     */
    private function checkMinMax (int $value, string $varName, ?int $min, ?int $max): void
    {
        if ($min !== null && $value < $min) {
            throw new InvalidArgumentException("Minimum value '{$varName}' is {$min}");
        }
        
        if ($max !== null && $value > $max) {
            throw new InvalidArgumentException("Maximum value '{$varName}' is {$max}");
        }
    }
    
    /**
     * @param int $start
     * @param int $end
     * @return void
     */
    private function checkStartEnd (int $start, int $end): void
    {
        if ($start > $end) {
            throw new InvalidArgumentException("Start value '{$start}' is out of range");
        }
    }
    
    /**
     * @param string $task
     * @return void
     */
    private function runTask (string $task): void
    {
        command($task);
    }
}