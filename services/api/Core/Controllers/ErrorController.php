<?php

namespace CloudCastle\Core\Controllers;

use CloudCastle\Core\Request\Request;
use CloudCastle\Core\Router\Router;
use Stringable;
use Throwable;

final class ErrorController extends Controller
{
    public function webPage404 (Request $request): Stringable|string
    {
        return view('errors/404', compact('request'));
    }
    
    public function webPage500 (Throwable $t): Stringable|string
    {
        return view('errors/500', ['error' => $t]);
    }
    
    public function jsonPage500 (Throwable $t): Stringable
    {
        return view('errors/404', compact('request'));
    }
    
    public function xmlPage500 (Throwable $t): Stringable
    {
        return new class($t) implements Stringable {
            
            private Throwable $t;
            
            public function __construct(Throwable $t){
                $this->t = $t;
            }
            public function __toString (): string
            {
                return json_encode([ Router::getCurrentRoute(), $this->t], JSON_PRETTY_PRINT);
            }
        };
    }
}