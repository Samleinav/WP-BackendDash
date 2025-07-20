<?php

use WPBackendDash\Helpers\WBERoute;

WBERoute::route(
    '^([0-9a-zA-Z_-]+/)?center/rooms/?$',
    '/wp-admin/admin.php?page=wbe_admin_page_chats_rooms',
);

WBERoute::route(
    '^([0-9a-zA-Z_-]+/)?center/orders/?$',
    '/wp-admin/admin.php?page=wbe_admin_page_orders',
    'QSA,NC,L'
);

WBERoute::route(
    '^([0-9a-zA-Z_-]+/)?center/orders/([0-9a-zA-Z-]+)/?$',
    '/wp-admin/admin.php?page=wbe_admin_page_chats_rooms&custom_order_serial=$2',
    'QSA,NC,L'
);


add_action('init', function () {
    //global $wp_rewrite;
	
    add_rewrite_rule(
        '^center/testpage/?$',
        'index.php?page=adminify_admin_page_testpage', // Cambiado el destino
        'top'
    );
	
	 add_rewrite_rule(
        '^center/orders/([0-9a-zA-Z-]+)/?$',
        'index.php?page=adminify_admin_page_order_view&custom_order_serial=$matches[1]',
        'top'
    );

    // 2. Regla para el listado general de órdenes: /center/orders/
    add_rewrite_rule(
        '^center/orders/?$',
        'index.php?page=adminify_admin_page_orders', // El slug de tu página de listado general
        'top'
    );
    //flush_rewrite_rules(); // Solo mientras pruebas

   // $rules = $wp_rewrite->rewrite_rules(); // Genera reglas si no existen

    //echo '<pre>' . print_r($wp_rewrite->rules, true) . '</pre>';
    //exit;
    //
    //
 
});

add_action( 'init', 'custom_parse_and_set_query_var' );
function custom_parse_and_set_query_var() {
    // Solo si estamos en el área de administración y la URL coincide con nuestro patrón
    // Verifica que estamos en admin y que la URL se parece a la que manejamos
    // Puedes refinar esta condición si es necesario.
    // $_GET['page'] verificará que estamos en tu página de administración específica.
	
        // Regex para capturar el número de serie de la URL amigable
        if (  isset($_GET["page"])  && isset($_GET["custom_order_serial"])
			&& $_GET["page"]  =="adminify_admin_page_order_view" 
			&& is_admin() ) {
            // Verifica si el número de serie está presente en la URL
            if ( !empty( $_GET["custom_order_serial"] ) ) {

                if ( ! session_id() ) { // Solo inicia la sesión si no hay una activa
                    session_start();
                }

                 $_SESSION['custom_order_serial'] = sanitize_text_field( $_GET['custom_order_serial'] );
            }
        }
}


