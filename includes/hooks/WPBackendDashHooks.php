<?php
namespace WPBackendDash\Hooks;

class WPBackendDashHooks {
    public static function init() {
        require_once plugin_dir_path(__FILE__) . 'wbe_admin_footer.php';
        require_once plugin_dir_path(__FILE__) . 'woocommerce/loader_woo.php';
        require_once plugin_dir_path(__FILE__) . 'wbe_remove_backend_notices.php';
    }

    public static function add_rewrite_rules() {
        // Ejemplo: URL personalizada
    }

    public static function custom_init_tasks() {
        // Código general como hooks personalizados, filtros, etc.
    }
}