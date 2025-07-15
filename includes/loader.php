<?php
defined('ABSPATH') || exit;

class WPBackendDashLoader {
    public static function load() {
        // Agrega hooks, shortcodes, API...
        require_once plugin_dir_path(__FILE__) . 'src/hooks.php';
        require_once plugin_dir_path(__FILE__) . 'src/rest-api.php';

       // WPBackendDashHooks::init();
    }
}
