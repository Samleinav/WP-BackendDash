<?php

use WPBackendDash\Helpers\WBERoute;
/**
 * Registra las rutas personalizadas para el plugin.
 * Routes for the WP Backend Dash plugin.
 */

// Routes for the Chats Rooms section
WBERoute::route(
    'center.rooms',
    'center/rooms',
    '/wp-admin/admin.php?page=wbe_admin_page_chats_rooms'
);
    WBERoute::route(
        'center.rooms.view',
        'center/rooms/{custom_room_id}/view',
        '/wp-admin/admin.php?page=wbe_admin_page_chats_room_view&custom_room_id=$2',
    );

    WBERoute::route(
        'center.rooms.edit',
        'center/rooms/{custom_room_id}/edit',
        '/wp-admin/admin.php?page=wbe_admin_page_chats_room_edit&custom_room_id=$2',
    );

    WBERoute::route(
        'center.rooms.create',
        'center/rooms/create',
        '/wp-admin/admin.php?page=wbe_admin_page_chats_room_create',
    );
// Routes for the Orders section
WBERoute::route(
    'center.orders',
    'center/orders',
    '/wp-admin/admin.php?page=wbe_admin_page_orders',
);

    WBERoute::route(
        'center.orders.view',
        'center/orders/{custom_order_serial}',
        '/wp-admin/admin.php?page=wbe_admin_page_chats_rooms&custom_order_serial=$2',
    );


