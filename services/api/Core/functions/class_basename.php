<?php

/**
 * @param string $class
 * @return string
 */
function class_basename (string $class): string
{
    return basename(str_replace('\\', DIRECTORY_SEPARATOR, $class), '.php');
}