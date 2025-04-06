<?php

function scan_dir ($dir): array
{
    $list = [];
    $files = scandir($dir);
    
    foreach ($files as $path) {
        
        if (!in_array($path, ['.', '..', basename(__FILE__)])) {
            $file = realpath($dir . DIRECTORY_SEPARATOR . $path);
            
            if (is_dir($file)) {
                $list = [...$list, ...scan_dir($file)];
            } else {
                $list[$path] = $file;
            }
        }
    }
    
    return $list;
}

foreach (scan_dir(__DIR__) as $file) {
    if (is_file($file)) {
        require_once $file;
    }
}