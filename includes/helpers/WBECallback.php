<?php
namespace WPBackendDash\Helpers;

use WPBackendDash\Helpers\WBERequest;
use ReflectionFunction;
use ReflectionMethod;

class WBECallback
{
    public static function resolve(callable $callback)
    {
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
}
