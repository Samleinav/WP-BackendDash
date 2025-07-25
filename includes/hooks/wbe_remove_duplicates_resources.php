<?php
/**
 * Revisa y elimina scripts (JS) y hojas de estilo (CSS) duplicados.
 *
 * Esta función se engancha a las acciones 'wp_print_scripts' y 'wp_print_styles'
 * para analizar los recursos en cola y eliminar aquellos con la misma URL de origen (src),
 * conservando solo la primera instancia encontrada.
 */
function mi_eliminar_recursos_duplicados() {
    // Variables globales de WordPress que almacenan los scripts y estilos
    global $wp_scripts, $wp_styles;

    // Asegurarse de que los objetos existen
    if ( ! is_object( $wp_scripts ) || ! is_object( $wp_styles ) ) {
        return;
    }

    $scripts_unicos = array();
    $estilos_unicos = array();

    // --- Procesa los Scripts (JS) ---
    // Itera sobre todos los scripts que están en cola para ser impresos
    foreach ( $wp_scripts->queue as $handle ) {
        // Obtiene el objeto del script a partir de su "handle" o identificador
        $script = $wp_scripts->registered[ $handle ];

        // Si la URL del script ya fue procesada, lo elimina de la cola.
        if ( isset( $scripts_unicos[ $script->src ] ) ) {
            wp_dequeue_script( $handle );
        } else {
            // Si es la primera vez que vemos esta URL, la registramos.
            $scripts_unicos[ $script->src ] = $handle;
        }
    }

    // --- Procesa los Estilos (CSS) ---
    // Itera sobre todos los estilos que están en cola para ser impresos
    foreach ( $wp_styles->queue as $handle ) {
        // Obtiene el objeto del estilo a partir de su "handle"
        $style = $wp_styles->registered[ $handle ];

        // Si la URL del estilo ya fue procesada, la elimina de la cola.
        if ( isset( $estilos_unicos[ $style->src ] ) ) {
            wp_dequeue_style( $handle );
        } else {
            // Si es la primera vez, la registramos.
            $estilos_unicos[ $style->src ] = $handle;
        }
    }
}

// Enganchamos nuestra función a los hooks que se ejecutan justo antes de renderizar los scripts y estilos.
// La prioridad 100 es para asegurarnos de que se ejecute después de que todos los plugins y el tema hayan agregado sus recursos.
add_action( 'wp_print_scripts', 'mi_eliminar_recursos_duplicados', 100 );
add_action( 'wp_print_styles', 'mi_eliminar_recursos_duplicados', 100 );