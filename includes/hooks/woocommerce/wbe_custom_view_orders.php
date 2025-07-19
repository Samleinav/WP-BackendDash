<?php

/**
 * Función de callback que renderiza el contenido de la página de vista de orden.
 */
add_shortcode("wc_custom_backend_order_view", "custom_backend_order_content_view" );
function custom_backend_order_content_view() {
	if ( ! session_id() ) { // Solo inicia la sesión si no hay una activa
      session_start();
    }
    // Obtiene el número de orden secuencial desde la URL
	$sequential_order_number = isset( $_SESSION['custom_order_serial'] ) ? $_SESSION['custom_order_serial'] : '';

    // Asegúrate de que el parámetro 'custom_order_serial' exista y no esté vacío
    if ( empty( $sequential_order_number ) ) {
        echo '<div class="wrap"><h1>Order Details</h1><p>Please provide a valid order number.</p></div>';
        return;
    }

    // *** IMPORTANTE: Reemplaza '_order_number' con la meta key real de tu plugin de números de orden secuenciales ***
    // Ejemplos comunes: '_wc_order_number', '_webtoffee_order_number', '_custom_order_number'
    $meta_key_for_sequential_number = '_order_number'; // Asume esta, pero verifica tu plugin
	$args = array(
        'limit'      => 1, // Queremos solo una orden ya que el número de serie debería ser único.
        'customer_id' => get_current_user_id(), // Opcional: filtra por el usuario actual si es necesario
        'meta_query' => array(
            array(
                'key'     => $meta_key_for_sequential_number,
                'value'   => $sequential_order_number,
                'compare' => '=',
            ),
        ),
        // No necesitamos 'post_type' ni 'post_status' si usamos 'wc_get_orders()'
        // ya que esta función ya opera sobre los tipos de post de orden y HPOS.
        // Opcional: Si quieres filtrar por un estado específico:
        // 'status' => 'wc-completed',
    );

    $orders = wc_get_orders( $args );

    // Si la orden no se encuentra o el array está vacío
    if ( empty( $orders ) ) {
        echo '<div class="wrap"><h1>Order Details</h1><p> #' . esc_html( $sequential_order_number ) . ' not found.</p></div>';
        return;
    }

    // Obtener el ID de la orden y el objeto WC_Order
    $order_id = $orders[0]->ID;
    $order = wc_get_order( $order_id );

    // Doble verificación del objeto de orden
    if ( ! $order ) {
        wp_die( 'Error loading order. The order object is not valid.', 'Error', array( 'response' => 500 ) );
    }

    // --- INICIO DEL CONTENIDO DE LA VISTA PERSONALIZADA ---
    ViewerHelper::view('woocommerce/custom_order_view.php', compact('order', 'sequential_order_number'));
}