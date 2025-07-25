<?php

namespace WPBackendDash\Helpers;

class WBEAPIManager {
    protected $namespace = 'wbe/v1';
    protected $routes = [];

    public function __construct($namespace = null) {
        if ($namespace) $this->namespace = $namespace;
        add_action('rest_api_init', [$this, 'register_routes']);
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
            register_rest_route($this->namespace, $route['route'], [
                'methods'             => $route['methods'],
                'callback'            => $route['callback'],
                'args'                => $route['args'],
                'permission_callback' => $route['permission_callback'] ?: '__return_true'
            ]);
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
