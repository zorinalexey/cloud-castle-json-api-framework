<?php

namespace CloudCastle\Core\Controllers;

use CloudCastle\Core\Request\Request;
use CloudCastle\Core\Router\Router;
use Stringable;
use Throwable;

final class ErrorController extends Controller
{
    public function page404(Request $request): Stringable
    {
        return new class($request) implements Stringable {
            
            private Request $request;
            
            public function __construct(Request $request){
                $this->request = $request;
            }
            public function __toString (): string
            {
                $current = Router::getCurrentRoute();
                
                return json_encode([$current, $this->request], JSON_PRETTY_PRINT);
            }
        };
    }
    
    public function page500(Throwable $t): Stringable
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