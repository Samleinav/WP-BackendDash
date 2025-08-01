<?php

use WPBackendDash\Helpers\WBERoute;
/**
 * Registra las rutas personalizadas para el plugin.
 * Routes for the WP Backend Dash plugin.
 */

// Routes for the Chats Rooms section
WBERoute::route(
    'center.rooms.index',
    'center/rooms',
    '/wp-admin/admin.php?page=wbe_admin_page_chats_rooms'
);
    WBERoute::route(
        'center.rooms.view',
        'center/rooms/{room_id}/start',
        '/wp-admin/admin.php?page=wbe_admin_page_chats_room_view&$room_id=$2',
    );

    WBERoute::route(
        'center.rooms.edit',
        'center/rooms/{room_id}/edit',
        '/wp-admin/admin.php?page=wbe_admin_page_chats_room_edit&$room_id=$2',
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
        'center/orders/{order_serial}',
        '/wp-admin/admin.php?page=wbe_admin_page_view_order&order_serial=$2',
    );


