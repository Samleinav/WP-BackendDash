<?php

namespace WPBackendDash\Includes;

use WPBackendDash\Includes\WBESourceLoader;
use WPBackendDash\Helpers\WBERoute;
use WPBackendDash\Helpers\WBEPage;
use WPBackendDash\Hooks\WPBackendDashHooks;

class WPBackendDashLoader {
    public static function load() {
        // Agrega hooks, shortcodes, API...
        require_once plugin_dir_path(__FILE__) . 'helpers/functions.php';
        
        self::loadResources();
        self::loadHooks();
            
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
}
