<?php

namespace Hanwoolderink\LaravelRoutes\Data;

class RouteData
{
    public function __construct(
        public string $method,
        public string $path,
        public string $controller,
        public string $controllerMethod,
        public array $middleware = [],
        public string $name = '',
    )
    {
        
    }
}
