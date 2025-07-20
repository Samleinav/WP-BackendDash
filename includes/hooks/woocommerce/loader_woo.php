<?php
// Cargar las funciones personalizadas de WooCommerce

// Asegúrate de que WooCommerce esté activo
    if ( ! class_exists( 'WooCommerce' ) ) {
        return 'WooCommerce no está activo. Por favor, activa WooCommerce para usar las funciones personalizadas.';
    }
    else {
        // Cargar las funciones de WooCommerce
        require_once plugin_dir_path( __FILE__ ) . 'wbe_woocommerce_hooks.php';
        require_once plugin_dir_path( __FILE__ ) . 'wbe_woo_redirect.php';
        require_once plugin_dir_path( __FILE__ ) . 'wbe_woo_edit_checkout_form.php';
    }