<?php
namespace WPBackendDash\Helpers;

use WPBackendDash\Helpers\WBERequest;
use WPBackendDash\Helpers\WBERoute;
use ReflectionFunction;
use ReflectionMethod;

class WBECallback
{
    public static function resolveOld($callback)
    {
        if (is_array($callback) && is_string($callback[0])) {
        // Convertimos [ClassName::class, 'method'] a [new ClassName, 'method']
        $callback[0] = new $callback[0];
        }

        return function () use ($callback) {
            $reflection = is_array($callback)
                ? new ReflectionMethod($callback[0], $callback[1])
                : new ReflectionFunction($callback);

            $args = [];

            foreach ($reflection->getParameters() as $param) {
                $type = $param->getType();
                $name = $param->getName();

                // Si es una clase, se instancia automáticamente
                if ($type && !$type->isBuiltin()) {
                    $className = $type->getName();
                    $args[] = new $className();
                }
                // Si es un nombre simple, se intenta extraer del request
                else {
                    $args[] = WBERequest::get($name);
                }
            }

            return call_user_func_array($callback, $args);
        };
    }

    public static function resolve($callback)
    {
        $match = WBERoute::matchCurrentRoute();
        if (!$match) {
            return '__return_null'; // o devolver una función vacía
        }

        if (is_array($callback) && is_string($callback[0])) {
            $callback[0] = new $callback[0];
        }

        $reflection = is_array($callback)
            ? new \ReflectionMethod($callback[0], $callback[1])
            : new \ReflectionFunction($callback);

        $params = $match['params'] ?? [];

        return function () use ($callback, $reflection, $params) {
            $args = [];

            foreach ($reflection->getParameters() as $param) {
                $type = $param->getType();
                $name = $param->getName();

                if ($type && !$type->isBuiltin()) {
                    $args[] = new ($type->getName())();
                } elseif (isset($params[$name])) {
                    $args[] = $params[$name];
                } else {
                    $args[] = WBERequest::get($name) ?? null;
                }
            }

            return call_user_func_array($callback, $args);
        };
    }

}
