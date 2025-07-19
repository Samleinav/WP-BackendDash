<?php
namespace WPBackendDash\Helpers;
// Asegúrate de que WBE_PLUGIN_PATH esté definido correctamente,
// preferiblemente sin una barra final para mayor consistencia.
// Ejemplo: define('WBE_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

class ViewerHelper {

    /**
     * Carga un archivo de vista y le pasa un array de variables.
     *
     * @param string $file_path La ruta del archivo de vista, relativa a WBE_PLUGIN_PATH.
     * @param array $data Un array asociativo de variables a pasar a la vista.
     * @return string El contenido HTML renderizado de la vista.
     */
    public static function view($file_path, $data = []) {
        // Asegura que el path tenga la extensión .php
        if (!str_ends_with($file_path, '.php')) {
            $file_path .= '.php';
        }

        // Construye la ruta completa del archivo de vista.
        // Asegura una barra final para WBE_PLUGIN_PATH.
        $full_path = trailingslashit(WBE_PLUGIN_PATH) . "src/views/" . $file_path;

        if (file_exists($full_path)) {
            // Extrae las variables del array $data en el ámbito local.
            // Esto hace que $data['variable_name'] sea accesible como $variable_name dentro del archivo incluido.
            if ( ! empty( $data ) && is_array( $data ) ) {
                extract($data); 
            }

            // Inicia el almacenamiento en búfer de salida para capturar el HTML.
            ob_start();
            include $full_path; // El archivo de vista se incluye aquí
            return ob_get_clean(); // Obtiene el contenido del búfer y lo limpia
        } else {
            error_log("Error: View file not found or inaccessible. Path attempted: " . $full_path);
            // Devuelve un comentario HTML en lugar de un error para evitar romper el front-end
            return ""; 
        }
    }
}
