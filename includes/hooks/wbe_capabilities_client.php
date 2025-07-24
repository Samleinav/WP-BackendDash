<?php

function add_client_capabilities() {
    // Agrega capacidades específicas para el cliente
    $role = get_role('client');
    if ($role) {
        $role->add_cap('wbe_view_orders');
        $role->add_cap('wbe_view_order_details');
        $role->add_cap('wbe_view_chats_rooms');
        $role->add_cap('wbe_view_chats_room_create');
        $role->add_cap('wbe_view_chats_room_edit');
        $role->add_cap('wbe_view_chats_room_view');
    }
    $role = get_role('administrator');
    if ($role) {
        $role->add_cap('wbe_view_orders');
        $role->add_cap('wbe_view_order_details');
        $role->add_cap('wbe_view_chats_rooms');
        $role->add_cap('wbe_view_chats_room_create');
        $role->add_cap('wbe_view_chats_room_edit');
        $role->add_cap('wbe_view_chats_room_view');
    }
}

add_action('init', 'add_client_capabilities');