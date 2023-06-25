<?php

namespace Hanwoolderink\LaravelRoutes\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        public string|array $method,
        public string $path,
        public array $middleware = [],
        public string $name = '',
    ) {
    }
}
