<?php

namespace WPBackendDash\Includes;

use WPBackendDash\Includes\WBESourceLoader;
use WPBackendDash\Helpers\WBERoute;
use WPBackendDash\Helpers\WBEAPIManager;    
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

        WBEAPIManager::init();

    }

    public static function loadHooks() {
        // Cargar hooks de la clase WPBackendDashHooks
        WPBackendDashHooks::init();
    }

    public static function assets() {

        WBEAssets::add_js(
            'notify-js',
            "https://cdn.jsdelivr.net/npm/simple-notify/dist/simple-notify.min.js",
            ['jquery'],
            '1.0.0',
        );

        WBEAssets::add_css(
            'notify-css',
            "https://cdn.jsdelivr.net/npm/simple-notify/dist/simple-notify.css",
            [],
            '1.0.0',
        );
        WBEAssets::add_js(
            'wpbackenddash-js',
            WBE_PLUGIN_URL. 'src/assets/js/wpbackenddash.js',
            ['jquery'],
            '1.0.0',
        );
        WBEAssets::add_js(
            'wpbackenddashactions-js',
            WBE_PLUGIN_URL. 'src/assets/js/wpbackendactions.js',
            ['jquery'],
            '1.0.0',
        );

        WBEAssets::add_css(
            'nice-forms-css',
            WBE_PLUGIN_URL . 'src/assets/css/nice-forms/nice-forms.css',
            [],
            '1.0.0',
        );

        WBEAssets::add_js(
            'jquerymodal-js',
            "https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js",
            ['jquery'],
            '1.0.0',
        );

        WBEAssets::add_css(
            'jquerymodal-css',
            "https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css",
            [],
            '1.0.0',
        );
        // Cargar assets
        WBEAssets::init();
    }

}
