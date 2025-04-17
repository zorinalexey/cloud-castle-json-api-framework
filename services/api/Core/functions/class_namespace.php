<?php

/**
 * @param string $class
 * @return string
 */
function class_namespace (string $class): string
{
    $classDir = dirname(str_replace('\\', DIRECTORY_SEPARATOR, $class));
    
    return str_replace(DIRECTORY_SEPARATOR, '\\', $classDir);
}