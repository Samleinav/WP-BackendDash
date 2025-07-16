<?php
/*
Plugin Name: WP Backend Dash
Description: Backend dashboard y control de entrevistas para AI.
Version: 1.0.2
Author: SamLeiNav
GitHub Plugin URI: https://github.com/Samleinav/WP-BackendDash
*/

defined('ABSPATH') || exit;

// Define our plugin version
if ( ! defined( 'WBE_PLUGIN_VERSION' ) ) {
    define('WBE_PLUGIN_VERSION', '1.0.2');
}


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
add_action( 'plugins_loaded', function() {

    // 1) Load our universal drop-in. Because that file begins with "namespace UUPD\V1;",
    //    both the class and the helper live under UUPD\V1.
    require_once __DIR__ . '/includes/helpers/updater.php';

    // 2) Build a single $updater_config array:
    $updater_config = [
        'plugin_file'   => plugin_basename(__FILE__),  // plugin_basename(__FILE__)
        'slug'          => 'wp-backenddash',                     // en minúsculas, sin espacios
        'name'          => 'WP Backend Dash',       
        'version'       => WBE_PLUGIN_VERSION,               // same as the VERSION constant above
        'server'        => 'https://raw.githubusercontent.com/Samleinav/WP-BackendDash/main/includes/index.json',  // GitHub or private server
        //'github_token'  => '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',             // optional
        //'server'      => 'https://updater.reallyusefulplugins.com/u/',
        // 'textdomain' is omitted, so the helper will automatically use 'slug'
        
    ];

    // 3) Call the helper in the UUPD\V1 namespace:
    \UUPD\V1\UUPD_Updater_V1::register( $updater_config );
}, 1 );

