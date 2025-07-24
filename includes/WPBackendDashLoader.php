<?php

namespace WPBackendDash\Includes;

use WPBackendDash\Includes\WBESourceLoader;
use WPBackendDash\Helpers\WBERoute;
use WPBackendDash\Helpers\WBEPage;
use WPBackendDash\Helpers\WBEAssets;
use WPBackendDash\Hooks\WPBackendDashHooks;

class WPBackendDashLoader {
    public static function load() {
        // Agrega hooks, shortcodes, API...
        require_once plugin_dir_path(__FILE__) . 'helpers/functions.php';
        
        self::loadResources();
        self::loadHooks();
        self::assets();
            
    }

    public static function loadResources() {  
        // Cargar rutas
        WBESourceLoader::init();
        //for testing purposes #need to remove
        WBERoute::applyChangesIfNeeded();
        // load pages
        WBEPage::init();

    }

    public static function loadHooks() {
        // Cargar hooks de la clase WPBackendDashHooks
        WPBackendDashHooks::init();
    }

    public static function assets() {

        WBEAssets::add_js(
            'notify-js',
            "https://cdnjs.cloudflare.com/ajax/libs/notify.js/2.0.0/notify.min.js",
            ['jquery'],
            '0.4.2',
        );
        WBEAssets::add_js(
            'wpbackenddash-js',
            WBE_PLUGIN_PATH. 'src/assets/js/wpbackenddash.js',
            ['jquery'],
            '1.0.0',
        );
        WBEAssets::add_js(
            'wpbackenddash-js',
            WBE_PLUGIN_PATH. 'src/assets/js/wpbackendactions.js',
            ['jquery'],
            '1.0.0',
        );
        // Cargar assets
        WBEAssets::init();
    }

}
