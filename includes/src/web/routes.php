<?php
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
   
        $request_uri = $_SERVER['REQUEST_URI'];
        $sequential_order_number = null;
	
        // Regex para capturar el número de serie de la URL amigable
        if (  isset($_GET["page"])  && isset($_GET["custom_order_serial"])
			&& $_GET["page"]  =="adminify_admin_page_order_view" 
			&& is_admin() ) {
            // Verifica si el número de serie está presente en la URL
            if ( !empty( $_GET["custom_order_serial"] ) ) {

                // Ahora, inyecta este valor en los query_vars de WordPress.
                // Accede a la instancia global de WP_Query
                global $wp_query;

                // Si $wp_query aún no está inicializado, es un poco temprano.
                // La forma más robusta es directamente a través del hook 'request'.
                // Pero si 'init' funciona para tu preg_match, podemos intentarlo aquí.
                // Sin embargo, 'parse_request' es más apropiado para manipular query_vars.

                // Mejor forma de asegurar que se establezca: directamente en $_GET para que WordPress lo recoja
                // si no tienes un filtro en 'parse_request'.
                // O usa $wp->query_vars = array_merge(...) si estás en 'parse_request'.

                // Si estás en 'init', puedes establecerlo en $_GET. WordPress lo procesará.
                // Ten en cuenta que esto es un poco una "trampa", pero funcional si necesitas mantener .htaccess.
                // $_GET['custom_order_serial'] = $sequential_order_number;

                // Alternativamente, forzarlo en los query_vars si $wp_query ya existe.
                // Es menos fiable que usar el hook 'parse_request' para esto.
                if ( isset( $wp_query ) && $wp_query instanceof WP_Query ) {
                    $wp_query->set( 'custom_order_serial', $sequential_order_number );
                }
            }
        }
}

