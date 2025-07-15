<?php
defined('ABSPATH') || exit;

class WPBackendDashLoader {
    public static function load() {
        // Agrega hooks, shortcodes, API...
        require_once plugin_dir_path(__FILE__) . 'src/web/routes.php';
        require_once plugin_dir_path(__FILE__) . 'src/models/models.php';
        require_once plugin_dir_path(__FILE__) . 'hooks/hooks.php';
        require_once plugin_dir_path(__FILE__) . 'helpers/functions.php';

       WPBackendDashHooks::init();
    }
}
