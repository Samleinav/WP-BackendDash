<?php

namespace WPBackendDash\Helpers;

use WPBackendDash\Helpers\WBERequest;
use ReflectionFunction;
use ReflectionMethod;

class WBEAPIManager {
    protected static $namespace = 'wbe/v1';
    protected static $routes = [];
    protected static $init = false;

    public static function init() {
        
        add_action('rest_api_init', function () {

            if (self::$init) {
                return;
            }
            WBEAPIManager::conditionally_register_route();

        });
    }

    public static function setNamespace($namespace) {
        self::$namespace = $namespace;
    }

    /**
     * Registra una nueva ruta en el API REST
     */
    public static function add_route($name, $route, $methods, $callback, $args = [], $permission_callback = null) {

      

        self::$routes[] = [
            'name' => $name,
            "full_route" => "wp-json/" .self::$namespace ."$route",
            'route' => $route,
            'methods' => $methods,
            'callback' => $callback,
            'args' => $args,
            'permission_callback' => $permission_callback
        ];
    }

    public static function getRoute($name){
        foreach (self::$routes as $route) {
            if ($route['name'] === $name) {
                return $route;
            }
        }
        return null; // Si no se encuentra la ruta
    }

    /**
     * GET route
     */
    public static function get($name, $route, $callback, $args = [], $permission_callback = null) {
        self::add_route($name, $route, 'GET', $callback, $args, $permission_callback);
    }

    /**
     * POST route
     */
    public static function post($name,$route, $callback, $args = [], $permission_callback = null) {
        self::add_route($name, $route, 'POST', $callback, $args, $permission_callback);

    }

    /**
     * PUT route
     */
    public static function put($name,$route, $callback, $args = [], $permission_callback = null) {
        self::add_route($name, $route, 'PUT', $callback, $args, $permission_callback);
    }

    /**
     * Permite registrar un método de clase como controlador
     */
    public function add_class_method_route($route, $methods, $class_instance, $method, $args = [], $permission_callback = null) {
        $callback = [$class_instance, $method];
        self::add_route($route, $methods, $callback, $args, $permission_callback);
    }

    /**
     * Registro final en WordPress -- no usando por ahora
     */
    public function register_routes() {
        foreach ($this->routes as $route) {

            $permission = $route['permission_callback'];
            $permission_callback = null;

            if ($permission === true) {
                $permission_callback = '__return_true';
            } elseif (is_string($permission)) {
                if (str_starts_with($permission, 'role:')) {
                    $role = substr($permission, 5);
                    $permission_callback = function () use ($role) {
                        return in_array($role, (array) wp_get_current_user()->roles);
                    };
                } else {
                    // Se asume como 'capability'
                    $permission_callback = function () use ($permission) {
                        return current_user_can($permission);
                    };
                }
            } elseif (is_callable($permission)) {
                $permission_callback = $permission;
            } else {
                $permission_callback = '__return_true';
            }

            register_rest_route($this->namespace, $route['route'], [
                'methods'             => $route['methods'],
                'callback'            => $route['callback'],
                'args'                => $route['args'],
                'permission_callback' => $permission_callback
            ]);
        }
    }

    /**
     * Registra las rutas condicionalmente
     */
    public static function conditionally_register_route() {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $path = trim(parse_url($request_uri, PHP_URL_PATH), '/');
        $path = preg_replace('#^wp-json/#', '', $path); // remove "wp-json/"

        foreach (self::$routes as $route) {
            $pattern = preg_quote($route['route'], '#');
            $pattern = preg_replace('#\\\\\{[^/]+\\\\\}#', '[^/]+', $pattern); // {var} -> [^/]+
            $namespace = trim(self::$namespace, '/');
            $pattern = "#^{$namespace}{$pattern}$#";

            $path = trim($path, '/');

            if (preg_match($pattern, $path) ) {

                $permission_callback = '__return_true';
                $permission = $route['permission_callback'];

                if ($permission === true) {
                    $permission_callback = '__return_true';
                } elseif (is_string($permission)) {
                    if (str_starts_with($permission, 'role:')) {
                        $role = substr($permission, 5);
                        $permission_callback = function () use ($role) {
                            return in_array($role, (array) wp_get_current_user()->roles);
                        };
                    } elseif (str_starts_with($permission, 'can:')) {
                        // Se asume como 'capability'
                        $permission_callback = function () use ($permission) {
                            return current_user_can(substr($permission, 4));
                        };

                    }elseif (str_starts_with($permission, 'login')) {
                        // Se asume como 'logged_in'
                        $permission_callback = function () {
                            return is_user_logged_in();
                        };

                    } else {
                        // Se asume como 'capability'
                        $permission_callback = function () use ($permission) {
                            return current_user_can($permission);
                        };
                    }
                } elseif (is_callable($permission)) {
                    $permission_callback = $permission;
                } else {
                    $permission_callback = '__return_true';
                }

               $callback = self::resolve($route['callback']);

                register_rest_route(self::$namespace, $route['route'], [
                    'methods'             => $route['methods'],
                    'callback'            =>  $callback,
                    'args'                => $route['args'],
                    'permission_callback' => $permission_callback,
                ]);

                break; // Solo registrar la primera que coincida
            }
        }
    }

    public static function matchCurrentRoute()
    {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $uri = preg_replace('#^wp-json/#', '', $uri); // remove "wp-json/"

        foreach (self::$routes as $route) {
            $pattern = '#^' . $route['regex'] . '$#';

            if (preg_match($pattern, $uri, $matches)) {
               array_shift($matches); // Quita el resultado completo

				// --- LA LÍNEA CLAVE ---
				// Filtra las capturas vacías y re-indexa el array para alinear los parámetros.
				// Esto elimina los 'placeholders' de los grupos opcionales que no coincidieron.
				$matches = array_values(array_filter($matches, 'strlen'));

				// Extraer nombres de la URL 'pretty'
				preg_match_all('/\{(\w+)\}/', $route['pretty'] ?? '', $nameMatches);
				$varNames = $nameMatches[1];

				// Combinar nombres con valores ya limpios
				$named = [];
				foreach ($varNames as $i => $name) {
					$named[$name] = $matches[$i] ?? null;
				}

                return [
					'route'   => $route,
					'params'  => $named, // Ahora ['custom_order_serial' => 'O000000000016514']
					'matches' => $matches,
				];
            }
        }

        return null;
    }

    public static function resolve($callback)
    {
        $match = self::matchCurrentRoute();

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

                    $className = $type->getName();
                    $instance = new $className();
                    
                    $args[] = $instance;

                } elseif (isset($params[$name])) {

                    $args[] = $params[$name];

                } else {

                    $args[] = WBERequest::get($name) ?? null;

                }
            }

            return call_user_func_array($callback, $args);
        };
    }

    public static function checkNonceRest() {
        $header = $_SERVER['X-WP-Nonce'];

        if (!wp_verify_nonce($header, 'wp_rest')) {
            return false;
        }

        return true;
    }
    /**
     * Permisos útiles
     */
    public static function require_login() {
        return is_user_logged_in();
    }

    public static function require_cap($capability) {
        return function () use ($capability) {
            return current_user_can($capability);
        };
    }

    public static function require_role($role) {
        return function () use ($role) {
            $user = wp_get_current_user();
            return in_array($role, (array) $user->roles);
        };
    }
}
