<?php

use CloudCastle\Core\View\View;
use CloudCastle\Core\View\ViewException;

/**
 * @param string $path
 * @param mixed ...$data
 * @return View
 * @throws ViewException
 */
function view (string $path, mixed ...$data): View
{
    return new View($path, ...$data);
}