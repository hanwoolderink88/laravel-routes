<?php

namespace Hanwoolderink\LaravelRoutes\Actions;

use Hanwoolderink\LaravelRoutes\Attributes\Middleware;
use Hanwoolderink\LaravelRoutes\Attributes\Route;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\Facades\Route as FacadesRoute;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

class GetRoutesInNamespace
{
    public function __invoke(string $namespace)
    {
        return $this->handle($namespace);
    }

    public function handle(string $namespace)
    {
        ClassFinder::disablePSR4Vendors();
        $classNames = ClassFinder::getClassesInNamespace($namespace);

        foreach ($classNames as $className) {
            $classReflection = new ReflectionClass($className);
            $methodReflections = $classReflection->getMethods(ReflectionMethod::IS_PUBLIC);
            $classMiddleware = $classReflection->getAttributes(Middleware::class);

            foreach ($methodReflections as $methodReflection) {
                $attributeReflections = $methodReflection->getAttributes(Route::class);

                if (count($attributeReflections) > 0) {
                    $routeMiddleware = $methodReflection->getAttributes(Middleware::class);
                    $middleware = array_merge($classMiddleware, $routeMiddleware);

                    $this->createRoute($attributeReflections[0], $methodReflection, $middleware);
                }
            }
        }
    }

    private function createRoute(ReflectionAttribute $attributes, ReflectionMethod $methodReflection, array $middlewareReflections)
    {
        /** @var Route $route */
        $routevalues = $attributes->newInstance();

        $route = FacadesRoute::addRoute(
            $routevalues->method,
            $routevalues->path,
            [$methodReflection->class, $methodReflection->name]
        );

        if ($routevalues->name) {
            $route->name($routevalues->name);
        }

        if (count($middlewareReflections) > 0) {
            $middleware = [];

            foreach ($middlewareReflections as $middlewareReflection) {
                $middlewareValues = $middlewareReflection->newInstance();
                $middleware[] = $middlewareValues->middleware;
            }

            $route->middleware($middleware);
        }
    }
}
