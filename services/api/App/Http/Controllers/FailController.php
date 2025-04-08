<?php

namespace App\Http\Controllers;

use App\Http\Request\TestRequest;
use CloudCastle\Core\Controllers\Controller;

final class FailController extends Controller
{
    public function __invoke (TestRequest $request): mixed
    {
        $data = $request->validated();
        var_dump($data);
        return $data;
    }
}