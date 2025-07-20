<?php

namespace WPBackendDash\Includes;

class WBESourceLoader {
    public static function init() {
        // Aquí puedes agregar cualquier inicialización que necesites
        // Por ejemplo, cargar archivos, definir constantes, etc.
        require_once WBE_PLUGIN_PATH . 'src/web/pages.php';
        require_once WBE_PLUGIN_PATH . 'src/web/routes.php';
        require_once WBE_PLUGIN_PATH . 'src/web/api.php';

    }
}