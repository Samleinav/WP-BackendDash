<?php
namespace WPBackendDash\Web;

use WPBackendDash\Helpers\WBEPage;
use WPBackendDash\Controllers\WBEOrdersController;
use WPBackendDash\Controllers\WBEChatsRooms;

WBEPage::add(
    'wbe_admin_page_chats_rooms',
    __('Chats Rooms', 'wp-backend-dash'),
    [WBEChatsRooms::class, 'index'],
    'dashicons-admin-generic',
    'wbe_view_chats_rooms',
    0,
    true
);

    WBEPage::add(
        'wbe_admin_page_chats_room_create',
        __('Chats Rooms', 'wp-backend-dash'),
        [WBEChatsRooms::class, 'create'],
        'dashicons-admin-generic',
        'wbe_view_chats_room_create',
        1,
        false
    );

    WBEPage::add(
        'wbe_admin_page_chats_room_edit',
        __('Chats Rooms', 'wp-backend-dash'),
        [WBEChatsRooms::class, 'edit'],
        'dashicons-admin-generic',
        'wbe_view_chats_room_edit',
        1,
        false
    );

    WBEPage::add(
        'wbe_admin_page_chats_room_view',
        __('Chat Room View', 'wp-backend-dash'),
        [WBEChatsRooms::class, 'room_view'],
        'dashicons-admin-generic',
        'wbe_view_chats_room_view',
        1,
        false
    );

WBEPage::add(
    'wbe_admin_page_orders',
    __('Payments', 'wp-backend-dash'),
    [WBEOrdersController::class, 'index'],
    'dashicons-admin-generic',
    'wbe_view_orders',
    2,
    true
);

    WBEPage::add(
        'wbe_admin_page_view_order',
        __('Order Details', 'wp-backend-dash'),
        [WBEOrdersController::init(), 'view_order'],
        'dashicons-admin-generic',
        'wbe_view_order_details',
        2,
        false
    );