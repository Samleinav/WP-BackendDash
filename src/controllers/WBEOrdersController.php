<?php

namespace WPBackendDash\Controllers;

use WPBackendDash\Helpers\ControllerHelper;
use WPBackendDash\Helpers\WBERequest;

class WBEOrdersController extends ControllerHelper{
   
    /**
     * Renderiza la página de listado de órdenes.
     */
    public function index() {
    
        // Renderiza la vista con las órdenes
        return self::view('orders/index');
    }

    /**
     * Renderiza la vista de una orden específica.
     * 
     */
    public function view_order($order_serial) {
       
        // Obtiene el número de orden secuencial desde la URL
        $sequential_order_number = $order_serial;

        // Asegúrate de que el parámetro 'custom_order_serial' exista y no esté vacío
        if ( empty( $sequential_order_number ) ) {
            return '<div class="wrap"><h1>Order Details</h1><p>Please provide a valid order number.</p></div>';
        }

        
        $meta_key_for_sequential_number = '_order_number'; 
        $args = array(
            'limit'      => 1, 
            'customer_id' => self::getCurrentUserId(), 
            'meta_query' => array(
                array(
                    'key'     => $meta_key_for_sequential_number,
                    'value'   => $sequential_order_number,
                    'compare' => '=',
                ),
            ),
            // 'status' => 'wc-completed',
        );

        $orders = wc_get_orders( $args );

        // Si la orden no se encuentra o el array está vacío
        if ( empty( $orders ) ) {
            return '<div class="wrap"><h1>Order Details</h1><p> #' . esc_html( $sequential_order_number ) . ' not found.</p></div>';
        }

        // Obtener el ID de la orden y el objeto WC_Order
        $order_id = $orders[0]->ID;
        $order = wc_get_order( $order_id );

        // Doble verificación del objeto de orden
        if ( ! $order ) {
            wp_die( 'Error loading order. The order object is not valid.', 'Error', array( 'response' => 500 ) );
        }

        // --- INICIO DEL CONTENIDO DE LA VISTA PERSONALIZADA ---
        self::view('woocommerce/custom_order_view', compact('order', 'sequential_order_number'));
    }

}