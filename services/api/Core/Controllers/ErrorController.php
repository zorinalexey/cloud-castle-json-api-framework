<?php

namespace CloudCastle\Core\Controllers;

use CloudCastle\Core\Request\Request;
use Throwable;

final class ErrorController extends Controller
{
    public function page404(Request $request)
    {
        var_dump(__METHOD__, $request);
    }
    
    public function page500(Throwable $t)
    {
        var_dump(__METHOD__, $t);
    }
}