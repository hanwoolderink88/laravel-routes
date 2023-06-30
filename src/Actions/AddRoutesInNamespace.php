<?php

namespace Hanwoolderink\LaravelRoutes\Actions;

use Hanwoolderink\LaravelRoutes\Actions\Traits\AddsRoute;
use Hanwoolderink\LaravelRoutes\Data\RouteData;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Routing\Router;

class AddRoutesInNamespace
{
    use AddsRoute;

    public function __construct(
        private ReflectClassRoutes $getRoutesInClass,
        private Router $router
    ) {
    }

    public function __invoke(string $namespace)
    {
        return $this->handle($namespace);
    }

    protected function handle(string $namespace)
    {
        ClassFinder::disablePSR4Vendors();
        $classNames = ClassFinder::getClassesInNamespace($namespace);

        foreach ($classNames as $className) {
            $getRoutes = $this->getRoutesInClass;

            $getRoutes($className)->each(fn (RouteData $routeData) => $this->addRoute($routeData));
        }
    }
}
