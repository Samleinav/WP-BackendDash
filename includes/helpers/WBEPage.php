<?php

namespace WPBackendDash\Helpers;

use WPBackendDash\Helpers\WBECallback;
use WPBackendDash\Helpers\WBERoute;

class WBEPage {
    private static array $pages = [];

    /**
     * Agrega una página admin oculta (o visible si se desea).
     *
     * @param string   $slug      Slug único (?page=slug).
     * @param string   $title     Título de la página.
     * @param callable $callback  Función que renderiza el contenido.
     * @param string   $icon      (Opcional) Icono de la página si se muestra.
     * @param string   $capability Capacidad requerida. Default: 'manage_options'.
     * @param int      $position  (Opcional) Posición del menú si se muestra.
     * @param bool     $visible   Mostrar en el menú o no. Default: false (oculta).
     */
    public static function add(
        string $slug,
        string $title,
        callable|array|string $callback,
        string $icon = '',
        string $capability = 'manage_options',
        int $position = 100,
        bool $visible = false
    ): void {
        self::$pages[] = [
            'slug'       => $slug,
            'title'      => $title,
            'callback'   => $callback,
            'icon'       => $icon,
            'capability' => $capability,
            'position'   => $position,
            'visible'    => $visible,
        ];
    }

    /**
     * Inicializa las páginas registradas y aplica su visibilidad.
     */
    public static function init(): void {

        

        add_action('admin_menu', function () {

            $callback = function () {
                // Aquí podrías agregar lógica adicional si es necesario
            };

            $match = WBERoute::matchCurrentRoute();
        
            foreach (self::$pages as $page) {
                if ($match && isset($match["route"]) && str_contains($match["route"]["redirect"], $page['slug'])) {
                    $callback = WBECallback::resolve($page['callback']);
                }
                add_menu_page(
                    $page['title'],
                    $page['title'],
                    $page['capability'],
                    $page['slug'],
                    $callback,
                    $page['icon'],
                    $page['position']
                );

                // Ocultar si se definió como invisible
                if (!$page['visible']) {
                    remove_menu_page($page['slug']);
                }
            }
        });
    }
}
