<?php

namespace Hanwoolderink\LaravelRoutes\Actions;

use Hanwoolderink\LaravelRoutes\Attributes\Middleware;
use Hanwoolderink\LaravelRoutes\Attributes\Route;
use Hanwoolderink\LaravelRoutes\Data\RouteData;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionMethod;

class ReflectClassRoutes
{
    public function __invoke(string $className): Collection
    {
        return $this->handle($className);
    }

    protected function handle(string $className): Collection
    {
        $response = new Collection();

        $classReflection = new ReflectionClass($className);
        $methodReflections = $classReflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $classMiddleware = $classReflection->getAttributes(Middleware::class);

        foreach ($methodReflections as $methodReflection) {
            $attributeReflections = $methodReflection->getAttributes(Route::class);

            if (count($attributeReflections) > 0) {
                $routeMiddleware = $methodReflection->getAttributes(Middleware::class);

                $middleware = (new Collection(array_merge($classMiddleware, $routeMiddleware)))
                    ->map(function ($item) {
                        /** @var Middleware $data */
                        $data = $item->newInstance();

                        return $data->middleware;
                    })->toArray();

                /** @var Route $routeData */
                $routeData = $attributeReflections[0]->newInstance();

                $response->add(
                    new RouteData(
                        $routeData->method,
                        $routeData->path,
                        $methodReflection->class,
                        $methodReflection->name,
                        $middleware,
                        $routeData->name
                    )
                );
            }
        }

        return $response;
    }
}
