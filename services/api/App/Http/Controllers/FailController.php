<?php

namespace App\Http\Controllers;

use App\Http\Request\TestRequest;
use CloudCastle\Core\Controllers\Controller;
use CloudCastle\Core\Request\Request;

final class FailController extends Controller
{
    public function __invoke (TestRequest $request): mixed
    {
        $data = $request->validated();
        
        return $data;
    }
}