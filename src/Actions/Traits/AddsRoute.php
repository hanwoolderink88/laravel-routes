<?php

namespace Hanwoolderink\LaravelRoutes\Actions\Traits;

use Hanwoolderink\LaravelRoutes\Data\RouteData;

trait AddsRoute
{
    private function addRoute(RouteData $routeData)
    {
        $route = $this->router->addRoute(
            $routeData->method,
            $routeData->path,
            [$routeData->controller, $routeData->controllerMethod]
        );

        if ($routeData->name) {
            $route->name($routeData->name);
        }

        if (count($routeData->middleware) > 0) {
            $route->middleware($routeData->middleware);
        }
    }
}
