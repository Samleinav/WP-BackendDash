<?php


function custom_woocommerce_account_redirect() {
    // Comprueba si estamos en la página de cuenta de WooCommerce
    if ( is_account_page() && ! is_user_logged_in() ) {
        // Redirige solo si el usuario no ha iniciado sesión y está en la página /account
        wp_safe_redirect( home_url( '/center' ) );
        exit();
    } elseif ( is_account_page() && is_user_logged_in() && ! is_wc_endpoint_url() ) {
        // Redirige si el usuario ha iniciado sesión y está en la página base /account (no en un endpoint como /account/orders)
        wp_safe_redirect( home_url( '/center' ) );
        exit();
    }
}
add_action( 'template_redirect', 'custom_woocommerce_account_redirect' );

// Opcional: Redirigir el enlace del menú "Mi Cuenta" de WooCommerce directamente a /center
function custom_woocommerce_my_account_menu_link( $url, $endpoint ) {
    if ( 'dashboard' === $endpoint ) { // 'dashboard' es el endpoint por defecto de /account
        $url = home_url( '/center' );
    }
    return $url;
}
add_filter( 'woocommerce_get_endpoint_url', 'custom_woocommerce_my_account_menu_link', 10, 2 );
