<?php

/**
 * @param $dir
 * @return array
 */
function scan_dir ($dir): array
{
    if(!is_dir($dir)){
        return [];
    }
    
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

/**
 * @param string $dir
 * @return void
 */
function clear_dir(string $dir): void
{
    if(!is_dir($dir)){
        return;
    }
    
    foreach (scandir($dir) as $file) {
        if ($file != "." && $file != "..") {
            unlink($file);
        }
    }
}

foreach (scan_dir(__DIR__) as $file) {
    if (is_file($file)) {
        require_once $file;
    }
}