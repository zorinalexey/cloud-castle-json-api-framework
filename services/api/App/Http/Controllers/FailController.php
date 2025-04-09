<?php

namespace App\Http\Controllers;

use App\Http\Request\TestRequest;
use CloudCastle\Core\Controllers\Controller;
use CloudCastle\Core\Router\Router;
use Stringable;

final class FailController extends Controller
{
    public function __invoke(TestRequest $request): Stringable
    {
        
        return new class($request) implements Stringable {
            private $request;
            
            public function __construct($request){
                $this->request = $request;
            }
            
            public function __toString (): string
            {
                $current = Router::getCurrentRoute();
                $name = $current->getName();
                $route = route($name);
                $request = $this->request;
                
                return json_encode(compact('current', 'name', 'route', 'request'));
            }
        };
    }
}