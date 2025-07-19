<?php
/*
Plugin Name: WP Backend Dash
Description: Backend dashboard y control de entrevistas para AI.
Version: 1.5.1
Author: SamLeiNav
GitHub Plugin URI: https://github.com/Samleinav/WP-BackendDash
*/

defined('ABSPATH') || exit;


define('WBE_PLUGIN_PATH', plugin_dir_path(__FILE__));

spl_autoload_register(function ($class) {
    // Cargar solo clases con el namespace WPBackendDash
    if (strpos($class, 'WPBackendDash\\') !== 0) {
        return;
    }

    // Quitar el namespace base del nombre de clase
    $relative_class = substr($class, strlen('WPBackendDash\\'));

    // Separar por directorios
    $parts = explode('\\', $relative_class);
    $class_name = array_pop($parts); // Último segmento es el archivo (clase)

    // Convertir los directorios a minúsculas
    $path = '';
    foreach ($parts as $part) {
        $path .= strtolower($part) . '/';
    }

    // Nombre del archivo tal como viene en la clase
    $file_name = $class_name . '.php';

    // Directorios base a buscar
    $base_dirs = [
        __DIR__ . '/',
        __DIR__ . '/includes/',
        __DIR__ . '/src/',
    ];

    // Buscar archivo en cada base_dir
    foreach ($base_dirs as $base_dir) {
        $full_path = $base_dir . $path . $file_name;
        if (file_exists($full_path)) {
            require_once $full_path;
            return;
        }
    }
});


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
        WPBackendDash\Helpers\WBERoute::applyChangesIfNeeded();
        WPBackendDashInstaller::install();
    }

    public function init() {
        // Cargar hooks y funcionalidades permanentes
        \WPBackendDash\Includes\WPBackendDashLoader::load();
        \WPBackendDash\Helpers\WBEUpdater::init(__FILE__);
    }
}

// Lanzar plugin
WPBackendDash::instance();
