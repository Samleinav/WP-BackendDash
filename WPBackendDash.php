<?php
/*
Plugin Name: WP Backend Dash
Description: Backend dashboard y control de entrevistas para AI.
Version: 1.0.3
Author: SamLeiNav
GitHub Plugin URI: https://github.com/Samleinav/WP-BackendDash
*/

defined('ABSPATH') || exit;


define('WBE_PLUGIN_PATH', plugin_dir_path(__FILE__));

final class WPBackendDash {
    private static $instance = null;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Activación
        register_activation_hook(__FILE__, [$this, 'activate']);

        // Inicialización general
        add_action('plugins_loaded', [$this, 'init']);
    }

    public function activate() {
        require_once plugin_dir_path(__FILE__) . 'includes/installer.php';
        WPBackendDashInstaller::install();
    }

    public function init() {
        // Cargar hooks y funcionalidades permanentes
        require_once plugin_dir_path(__FILE__) . 'includes/loader.php';
        WPBackendDashLoader::load();
    }
}

// Lanzar plugin
WPBackendDash::instance();



// ──────────────────────────────────────────────────────────────────────────
//  Updater
// ──────────────────────────────────────────────────────────────────────────
require_once plugin_dir_path(__FILE__) . 'includes/helpers/updater.php';
add_action('init', function () {
    if (is_admin()) {
        new WPBackendDash_Updater(__FILE__);
    }
});