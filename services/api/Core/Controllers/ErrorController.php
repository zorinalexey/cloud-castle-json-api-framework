<?php

namespace CloudCastle\Core\Controllers;

use CloudCastle\Core\Request\Request;
use Throwable;

final class ErrorController extends Controller
{
    public function page404(Request $request): mixed
    {
        return $request;
    }
    
    public function page500(Throwable $t): mixed
    {
        return $t;
    }
}