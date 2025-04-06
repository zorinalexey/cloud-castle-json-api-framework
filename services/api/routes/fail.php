<?php

use App\Http\Controllers\FailController;
use App\Http\Request\TestRequest;
use CloudCastle\Core\Router\Route;

Route::any('{api_path}/{api_version}', FailController::class)->name('page.404')->request(TestRequest::class);