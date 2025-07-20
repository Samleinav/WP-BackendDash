<?php

function add_client_capabilities() {
    // Agrega capacidades específicas para el cliente
    $role = get_role('client');
    if ($role) {
        $role->add_cap('wbe_view_orders');
        $role->add_cap('wbe_view_order_details');
        // Agrega más capacidades según sea necesario
    }
}
add_action('init', 'add_client_capabilities');