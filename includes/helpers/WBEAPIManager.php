<?php

namespace WPBackendDash\Helpers;

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

                if(!self::checkNonceRest()) {
                    return new \WP_Error('rest_forbidden', 'No tienes permiso para acceder a esta ruta.', ['status' => 403]);
                }

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

                if(is_string($route['callback']) && strpos($route['callback'], '@') !== false) {
                    // Si es un string con formato "Clase@metodo"
                    $parts = explode('@', $route['callback']);
                    $class = $parts[0];
                    $method = $parts[1];
                    $route['callback'] = [new $class(), $method];
                }elseif (is_array($route['callback']) && count($route['callback']) === 2 && is_string($route['callback'][0]) && is_string($route['callback'][1])) {
                    // Si es un array con formato [objeto, metodo]
                    $route['callback'] = [ new $route['callback'][0](), $route['callback'][1]];
                }

                register_rest_route(self::$namespace, $route['route'], [
                    'methods'             => $route['methods'],
                    'callback'            => $route['callback'],
                    'args'                => $route['args'],
                    'permission_callback' => $permission_callback,
                ]);

                break; // Solo registrar la primera que coincida
            }
        }
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
