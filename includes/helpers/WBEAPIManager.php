<?php

namespace WPBackendDash\Helpers;

class WBEAPIManager {
    protected $namespace = 'wbe/v1';
    protected $routes = [];

    public function __construct($namespace = null) {
        if ($namespace) $this->namespace = $namespace;
        add_action('rest_api_init', [$this, 'conditionally_register_route']);
    }

    /**
     * Registra una nueva ruta en el API REST
     */
    public function add_route($route, $methods, $callback, $args = [], $permission_callback = null) {
        $this->routes[] = [
            'route' => $route,
            'methods' => $methods,
            'callback' => $callback,
            'args' => $args,
            'permission_callback' => $permission_callback
        ];
    }

    /**
     * GET route
     */
    public function get($route, $callback, $args = [], $permission_callback = null) {
        $this->add_route($route, 'GET', $callback, $args, $permission_callback);
    }

    /**
     * POST route
     */
    public function post($route, $callback, $args = [], $permission_callback = null) {
        $this->add_route($route, 'POST', $callback, $args, $permission_callback);

    }

    /**
     * PUT route
     */
    public function put($route, $callback, $args = [], $permission_callback = null) {
        $this->add_route($route, 'PUT', $callback, $args, $permission_callback);
    }

    /**
     * Permite registrar un método de clase como controlador
     */
    public function add_class_method_route($route, $methods, $class_instance, $method, $args = [], $permission_callback = null) {
        $callback = [$class_instance, $method];
        $this->add_route($route, $methods, $callback, $args, $permission_callback);
    }

    /**
     * Registro final en WordPress
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

    public static function add_lazy_route($pretty, $methods, $callback, $args = [], $permission = null) {
        self::$routes[] = compact('pretty', 'methods', 'callback', 'args', 'permission');
    }

    public function conditionally_register_route() {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $path = trim(parse_url($request_uri, PHP_URL_PATH), '/');
        $path = preg_replace('#^wp-json/#', '', $path); // remove "wp-json/"

        foreach (self::$routes as $route) {
            $pattern = preg_quote($route['pretty'], '#');
            $pattern = preg_replace('#\\\\\{[^/]+\\\\\}#', '[^/]+', $pattern); // {var} -> [^/]+
            $pattern = "#^{$this->namespace}/{$pattern}$#";

            if (preg_match($pattern, $path)) {

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

                register_rest_route($this->namespace, $route['pretty'], [
                    'methods'             => $route['methods'],
                    'callback'            => $route['callback'],
                    'args'                => $route['args'],
                    'permission_callback' => $permission_callback,
                ]);

                break; // Solo registrar la primera que coincida
            }
        }
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
