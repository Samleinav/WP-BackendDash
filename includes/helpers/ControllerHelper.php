<?php

namespace WPBackendDash\Helpers;
use WPBackendDash\Helpers\WBERequest;

class ControllerHelper {
    /**
     * Renderiza una vista con los datos proporcionados.
     *
     * @param string $view Nombre de la vista a renderizar.
     * @param array $data Datos a pasar a la vista.
     */
    public static function view($view, $data = [], $echo = true) {
        if($echo) {
            // Si se requiere imprimir la vista directamente
            echo self::renderView($view, $data);
        } else {
            // Si se requiere devolver la vista como string
            return self::renderView($view, $data);
        }
    }

    /**
     * Renderiza una vista y devuelve el contenido como string.
     *
     * @param string $view Nombre de la vista a renderizar.
     * @param array $data Datos a pasar a la vista.
     * @return string Contenido renderizado de la vista.
     */
    public static function renderView($view, $data = []) {
        return ViewerHelper::view($view, $data);
    }

    /**
     * Redirige a una URL específica.
     *
     * @param string $url URL a la que redirigir.
     */
    public static function redirect($url) {
        wp_redirect($url);
        exit;
    }

    /**
     * Obtiene el slug de la página actual.
     *
     * @return string Slug de la página actual.
     */
    public static function getCurrentPageSlug() {
        return isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
    }

    /**
     * Verifica si el usuario tiene permisos para acceder a una página específica.
     * 
     * @param string $pageSlug Slug de la página a verificar.
     * @return bool Verdadero si el usuario tiene permisos, falso en caso contrario.
     */
    public static function hasPageAccess($pageSlug) {
        // Verifica si el usuario tiene la capacidad requerida para la página
        return current_user_can('manage_options') || current_user_can($pageSlug);
    }

    /**
     * Obtiene el ID del usuario actual.
     *
     * @return int ID del usuario actual.
     */
    public static function getCurrentUserId() {
        return get_current_user_id();
    }

    /**
     * Obtiene el objeto del usuario actual.
     *
     * @return WP_User Objeto del usuario actual.
     */
    public static function getCurrentUser() {
        return wp_get_current_user();
    }

    /**
     * Verifica si el usuario actual es un administrador.
     *
     * @return bool Verdadero si el usuario es administrador, falso en caso contrario.
     */
    public static function isCurrentUserAdmin() {
        return current_user_can('administrator');
    }


    /**
     * Instancia de la clase ControllerHelper.
     * @return static
     */
     public static function init() {
        return new static(); 
    }


    public function response() {
        return WBERequest::Response();
    }

    public function request() {
        return new WBERequest;
    }

    protected function file_resource(){
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }
    

    public function uploadFile($file, $options = ['test_form' => false, 'mimes' => []] ) {
        $this->file_resource();
        $upload = wp_handle_upload($file,$options );
        if (isset($upload['error'])) {
            return new \WP_Error('upload_error', $upload['error']);
        }

        $wp_upload_dir = wp_upload_dir();
        $attachment = [
            'guid'           => $upload['url'],
            'post_mime_type' => $upload['type'],
            'post_title'     => sanitize_file_name($file['name']),
            'post_content'   => '',
            'post_status'    => 'inherit',
            'post_author'    => get_current_user_id(),
        ];
        $attachment_id = wp_insert_attachment($attachment, $upload['file']);

        $attach_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
        wp_update_attachment_metadata($attachment_id, $attach_data);

        $filedata = [
            'file_id' => $attachment_id,
            'file'          => $upload['file'],
            'url'           => $upload['url'],
        ];

        return $filedata;
    }
}

