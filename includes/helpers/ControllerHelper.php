<?php

namespace WPBackendDash\Helpers;

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
}

