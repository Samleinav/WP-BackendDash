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
            WBEAPIManager::conditionally_register_routev2();

        });
    }

    public static function setNamespace($namespace) {
        self::$namespace = $namespace;
    }

    /**
     * Registra una nueva ruta en el API REST
     */
    public static function add_route($name, $route, $methods, $callback, $args = [], $permission_callback = null) {

        $wp_route = self::convert_pretty_route_to_wp($route, $args);


        self::$routes[] = [
            'name' => $name,
            "full_route" => "wp-json/" .self::$namespace ."$route",
            'route' => $wp_route['route'],
            'methods' => $methods,
            'callback' => $callback,
            'args' => $wp_route['args'],
            'permission_callback' => $permission_callback
        ];
    }

    private static function convert_pretty_route_to_wp(string $prettyRoute, array $customArgs = []): array {
        $pattern = preg_replace_callback('/\{(\w+)\}/', function ($matches) use (&$args) {
            $param = $matches[1];
            $args[$param] = [
                'required' => true,
                'validate_callback' => function ($value) {
                    return is_string($value) && strlen($value) > 0;
                }
            ];
            return '(?P<' . $param . '>[^/]+)';
        }, $prettyRoute);

        $args = $args ?? [];

        return [
            'route' => $pattern,
            'args' => array_merge($args, $customArgs),
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

    public static function route($method, $name, $route, $callback, $args = [], $permission_callback = null) {
        self::add_route($name, $route, strtoupper($method), $callback, $args, $permission_callback);
    }

    private static function normalize_permission_callback($permission) {
        if ($permission === true) {
            return '__return_true';
        }

        if (is_string($permission)) {
            if (str_starts_with($permission, 'role:')) {
                $role = substr($permission, 5);
                return fn() => in_array($role, (array) wp_get_current_user()->roles);
            }

            if (str_starts_with($permission, 'can:')) {
                return fn() => current_user_can(substr($permission, 4));
            }

            if (str_starts_with($permission, 'login')) {
                return fn() => is_user_logged_in();
            }

            return fn() => current_user_can($permission);
        }

        if (is_callable($permission)) {
            return $permission;
        }

        return '__return_true';
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
    public static function conditionally_register_routev2() {
        $match = self::matchCurrentRoute();
        if (!$match) return;

        $route = $match['route'];
        register_rest_route(
            self::$namespace,
            $route['route'],
            [
                'methods'             => $route['methods'],
                'callback'            => self::resolvev2($route['callback'], $match['params']),
                'args'                => $route['args'],
                'permission_callback' => self::normalize_permission_callback($route['permission_callback']),
            ]
        );
    }

    public static function resolvev2($callback, $params = null) {
        $params ??= self::matchCurrentRoute()['params'] ?? [];

        if (is_array($callback) && is_string($callback[0])) {
            $callback[0] = new $callback[0];
        }

        $reflection = is_array($callback)
            ? new ReflectionMethod($callback[0], $callback[1])
            : new ReflectionFunction($callback);

        return function () use ($callback, $reflection, $params) {
            $args = [];

            foreach ($reflection->getParameters() as $param) {
                $type = $param->getType();
                $name = $param->getName();

                if ($type && !$type->isBuiltin()) {
                    $args[] = new ($type->getName());
                } elseif (array_key_exists($name, $params)) {
                    $args[] = $params[$name];
                } else {
                    $args[] = WBERequest::get($name) ?? null;
                }
            }

            return call_user_func_array($callback, $args);
        };
    }

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

    private static function prettyToRegex(string $pretty): string
    {
        $regex = preg_replace_callback('/\{(\w+)\}/', function () {
            return '([^/]+)';
        }, $pretty);

        return '^' . trim($regex, '/') . '/?$';
    }

    public static function matchCurrentRoute()
    {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        foreach (self::$routes as $route) {
            
            $regex = self::prettyToRegex($route['full_route']);
            $pattern = '#^' . $regex . '$#';

            if (preg_match($pattern, $uri, $matches)) {
               array_shift($matches); // Quita el resultado completo

				// --- LA LÍNEA CLAVE ---
				// Filtra las capturas vacías y re-indexa el array para alinear los parámetros.
				// Esto elimina los 'placeholders' de los grupos opcionales que no coincidieron.
				$matches = array_values(array_filter($matches, 'strlen'));

				// Extraer nombres de la URL 'pretty'
				preg_match_all('/\{(\w+)\}/', $route['full_route'] ?? '', $nameMatches);
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
