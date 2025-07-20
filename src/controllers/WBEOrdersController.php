<?php

namespace WPBackendDash\Controllers;

use WPBackendDash\Helpers\ControllerHelper;
use WPBackendDash\Helpers\WBERequest;

class WBEOrdersController extends ControllerHelper{
    /**
     * Constructor para inicializar el controlador.
     */
    public function __construct() {
       
        // Aquí puedes inicializar cualquier cosa específica del controlador de órdenes
         if ( ! session_id() ) { // Solo inicia la sesión si no hay una activa
            session_start();
        }
    }
    /**
     * Renderiza la página de listado de órdenes.
     */
    public function index() {
        // Verifica si el usuario tiene acceso a esta página
        //if (!self::hasPageAccess('wbe_admin_page_orders')) {
        //    wp_die(__('No tienes permiso para acceder a esta página.', 'wp-backend-dash'));
        //}

        // Renderiza la vista con las órdenes
        return self::view('orders/index');
    }

    /**
     * Renderiza la vista de una orden específica.
     * 
     */
    public function view_order(){
       
        // Obtiene el número de orden secuencial desde la URL
        $sequential_order_number = isset( $_SESSION['custom_order_serial'] ) ? $_SESSION['custom_order_serial'] : '';

        // Asegúrate de que el parámetro 'custom_order_serial' exista y no esté vacío
        if ( empty( $sequential_order_number ) ) {
            return '<div class="wrap"><h1>Order Details</h1><p>Please provide a valid order number.</p></div>';
        }

        // *** IMPORTANTE: Reemplaza '_order_number' con la meta key real de tu plugin de números de orden secuenciales ***
        // Ejemplos comunes: '_wc_order_number', '_webtoffee_order_number', '_custom_order_number'
        $meta_key_for_sequential_number = '_order_number'; // Asume esta, pero verifica tu plugin
        $args = array(
            'limit'      => 1, // Queremos solo una orden ya que el número de serie debería ser único.
            'customer_id' => self::getCurrentUserId(), // Opcional: filtra por el usuario actual si es necesario
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