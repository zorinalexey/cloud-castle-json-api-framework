<?php

function command (string $command, array $args = [])
{
    $arguments = ' ';
    
    foreach ($args as $key => $value) {
        $arguments .= "{$key}=\"{$value}\" ";
    }
    
    $logFile = config('console.log_file', '/var/log/' . date('Y-m-d') . '-console.log');
    $command = 'php '. APP_ROOT . '/console ' . $command . $arguments . ' >> "' . $logFile . '" 2>&1 & echo $!';
    exec($command);
}