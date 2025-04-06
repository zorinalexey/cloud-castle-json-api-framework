<?php

namespace CloudCastle\Core\Middleware;

use CloudCastle\Core\Request\RequestInterface;

abstract class Middleware
{
    abstract public function handle (RequestInterface $request): bool;
}