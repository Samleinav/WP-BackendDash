<?php

add_filter( 'woocommerce_checkout_fields', 'custom_remove_checkout_fields_for_virtual' );

function custom_remove_checkout_fields_for_virtual( $fields ) {

    // Detectar si todos los productos son virtuales
    $only_virtual = true;
    foreach ( WC()->cart->get_cart() as $cart_item ) {
        if ( ! $cart_item['data']->is_virtual() ) {
            $only_virtual = false;
            break;
        }
    }

    // Si todos son virtuales, eliminamos campos innecesarios
    if ( $only_virtual ) {
        unset( $fields['billing']['billing_first_name'] );
        unset( $fields['billing']['billing_last_name'] );
        unset( $fields['billing']['billing_company'] );
        unset( $fields['billing']['billing_country'] );
        unset( $fields['billing']['billing_address_1'] );
        unset( $fields['billing']['billing_address_2'] );
        unset( $fields['billing']['billing_city'] );
        unset( $fields['billing']['billing_state'] );
        unset( $fields['billing']['billing_postcode'] );
        unset( $fields['billing']['billing_phone'] );
        // Email sí se recomienda dejarlo (por temas legales y notificaciones), pero puedes quitarlo si lo deseas:
        // unset( $fields['billing']['billing_email'] );

        // También puedes quitar el campo de notas de pedido
        unset( $fields['order']['order_comments'] );
    }

    return $fields;
}
