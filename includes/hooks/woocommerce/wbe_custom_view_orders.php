<?php
// En functions.php de tu tema hijo o un plugin personalizado

function custom_order_query_vars( $vars ) {
    $vars[] = 'custom_order_serial';
    return $vars;
}
add_filter( 'query_vars', 'custom_order_query_vars' );

/**
 * Función de callback que renderiza el contenido de la página de vista de orden.
 */
add_shortcode("wc_custom_backend_order_view", "custom_backend_order_content_view" );
function custom_backend_order_content_view() {
	
  	$sequential_order_number = null;
	$request_uri = $_SERVER['REQUEST_URI'];
	if ( preg_match( '#/center/orders/([0-9a-zA-Z-]+)/?#', $request_uri, $matches ) ) {
        if ( isset( $matches[1] ) && ! empty( $matches[1] ) ) {
            $sequential_order_number = sanitize_text_field( $matches[1] );
        }
    }
	 global $wp_query; // Accede a la instancia global de WP_Query

    echo '<div class="wrap">';
    echo '<h1>Variables de Consulta ($wp_query->query_vars)</h1>';

    if ( ! empty( $wp_query->query_vars ) ) {
        echo '<pre>';
        print_r( $wp_query->query_vars );
        echo '</pre>';
    } else {
        echo '<p>No se encontraron variables de consulta en $wp_query->query_vars.</p>';
    }

    echo '</div>';
	echo $request_uri;
	 echo '///////////';
	echo $sequential_order_number ;
	exit;
	
    // Asegúrate de que el parámetro 'custom_order_serial' exista y no esté vacío
    if ( empty( $sequential_order_number ) ) {
        echo '<div class="wrap"><h1>Detalles de la Orden</h1><p>Por favor, proporciona un número de orden válido.</p></div>';
        return;
    }

    // Sanitizar el número de orden secuencial de la URL
    $sequential_order_number = sanitize_text_field( wp_unslash( $_GET['custom_order_serial'] ) );

    // *** IMPORTANTE: Reemplaza '_order_number' con la meta key real de tu plugin de números de orden secuenciales ***
    // Ejemplos comunes: '_wc_order_number', '_webtoffee_order_number', '_custom_order_number'
    $meta_key_for_sequential_number = '_order_number'; // Asume esta, pero verifica tu plugin
	$args = array(
        'limit'      => 1, // Queremos solo una orden ya que el número de serie debería ser único.
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

    $orders = get_posts( $args );

    // Si la orden no se encuentra o el array está vacío
    if ( empty( $orders ) ) {
        echo '<div class="wrap"><h1>Detalles de la Orden</h1><p>La orden con el número secuencial #' . esc_html( $sequential_order_number ) . ' no fue encontrada o no tienes permiso para verla.</p></div>';
        return;
    }

    // Obtener el ID de la orden y el objeto WC_Order
    $order_id = $orders[0]->ID;
    $order = wc_get_order( $order_id );

    // Doble verificación del objeto de orden
    if ( ! $order ) {
        wp_die( 'Error al cargar la orden. El objeto de orden no es válido.', 'Error', array( 'response' => 500 ) );
    }

    // --- INICIO DEL CONTENIDO DE LA VISTA PERSONALIZADA ---
    ?>
    <div class="wrap">
        <h1>Detalles de la Orden #<?php echo esc_html( $order->get_order_number() ); ?></h1>

        <style>
            /* Estilos básicos para la vista de la orden dentro del admin */
            .order-details-card { margin-bottom: 20px; border: 1px solid #ddd; padding: 15px; border-radius: 4px; background: #fdfdfd; }
            .order-details-card h3 { margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #eee; }
            .order-details-card p { margin: 5px 0; }
            .order-items table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            .order-items th, .order-items td { border: 1px solid #eee; padding: 8px; text-align: left; }
            .order-items th { background-color: #f5f5f5; }
            .order-summary { text-align: right; margin-top: 20px; }
            .order-summary p { font-size: 1.1em; font-weight: bold; }
        </style>

        <div class="order-details-card">
            <h3>Información General</h3>
            <p><strong>Estado:</strong> <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></p>
            <p><strong>Fecha de la Orden:</strong> <?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></p>
            <p><strong>Método de Pago:</strong> <?php echo esc_html( $order->get_payment_method_title() ); ?></p>
        </div>

        <div class="order-details-card">
            <h3>Productos</h3>
            <div class="order-items">
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $order->get_items() as $item_id => $item ) :
                            $_product = $item->get_product(); // No se usa directamente pero es buena práctica tenerlo si se necesitan más detalles del producto.
                            ?>
                            <tr>
                                <td><?php echo esc_html( $item->get_name() ); ?></td>
                                <td><?php echo esc_html( $item->get_quantity() ); ?></td>
                                <td><?php echo wc_price( $item->get_subtotal() / $item->get_quantity() ); ?></td>
                                <td><?php echo wc_price( $item->get_total() ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="order-summary">
                <p>Subtotal: <?php echo wc_price( $order->get_subtotal() ); ?></p>
                <?php if ( $order->get_shipping_total() > 0 ) : ?>
                    <p>Envío: <?php echo wc_price( $order->get_shipping_total() ); ?></p>
                <?php endif; ?>
                <?php if ( $order->get_total_tax() > 0 ) : ?>
                    <p>Impuestos: <?php echo wc_price( $order->get_total_tax() ); ?></p>
                <?php endif; ?>
                <p>Total: <?php echo wc_price( $order->get_total() ); ?></p>
            </div>
        </div>

        <div class="order-details-card">
            <h3>Dirección de Facturación</h3>
            <p><strong>Nombre:</strong> <?php echo esc_html( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ); ?></p>
            <p><strong>Compañía:</strong> <?php echo esc_html( $order->get_billing_company() ); ?></p>
            <p><strong>Dirección 1:</strong> <?php echo esc_html( $order->get_billing_address_1() ); ?></p>
            <?php if ( $order->get_billing_address_2() ) : ?>
                <p><strong>Dirección 2:</strong> <?php echo esc_html( $order->get_billing_address_2() ); ?></p>
            <?php endif; ?>
            <p><strong>Ciudad:</strong> <?php echo esc_html( $order->get_billing_city() ); ?></p>
            <p><strong>Estado/Provincia:</strong> <?php echo esc_html( $order->get_billing_state() ); ?></p>
            <p><strong>Código Postal:</strong> <?php echo esc_html( $order->get_billing_postcode() ); ?></p>
            <p><strong>País:</strong> <?php echo esc_html( $order->get_billing_country() ); ?></p>
            <p><strong>Email:</strong> <?php echo esc_html( $order->get_billing_email() ); ?></p>
            <p><strong>Teléfono:</strong> <?php echo esc_html( $order->get_billing_phone() ); ?></p>
        </div>

        <div class="order-details-card">
            <h3>Dirección de Envío</h3>
            <?php if ( wc_ship_to_billing_address_only() || $order->get_shipping_first_name() === $order->get_billing_first_name() && $order->get_shipping_last_name() === $order->get_billing_last_name() && $order->get_shipping_address_1() === $order->get_billing_address_1() ) : ?>
                <p>Misma que la dirección de facturación.</p>
            <?php else : ?>
                <p><strong>Nombre:</strong> <?php echo esc_html( $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name() ); ?></p>
                <p><strong>Compañía:</strong> <?php echo esc_html( $order->get_shipping_company() ); ?></p>
                <p><strong>Dirección 1:</strong> <?php echo esc_html( $order->get_shipping_address_1() ); ?></p>
                <?php if ( $order->get_shipping_address_2() ) : ?>
                    <p><strong>Dirección 2:</strong> <?php echo esc_html( $order->get_shipping_address_2() ); ?></p>
                <?php endif; ?>
                <p><strong>Ciudad:</strong> <?php echo esc_html( $order->get_shipping_city() ); ?></p>
                <p><strong>Estado/Provincia:</strong> <?php echo esc_html( $order->get_shipping_state() ); ?></p>
                <p><strong>Código Postal:</strong> <?php echo esc_html( $order->get_shipping_postcode() ); ?></p>
                <p><strong>País:</strong> <?php echo esc_html( $order->get_shipping_country() ); ?></p>
            <?php endif; ?>
        </div>

    </div>
    <?php
}