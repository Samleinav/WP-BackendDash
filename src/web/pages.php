<?php
namespace WPBackendDash\Web;

use WPBackendDash\Helpers\WBEPage;
use WPBackendDash\Controllers\WBEOrdersController;
use WPBackendDash\Controllers\WBEChatsRooms;

WBEPage::add(
    'wbe_admin_page_chats_rooms',
    __('Chats Rooms', 'wp-backend-dash'),
    [WBEChatsRooms::init(), 'index'],
    'dashicons-admin-generic',
    'wbe_view_chats_rooms',
    0,
    true
);

WBEPage::add(
    'wbe_admin_page_orders',
    __('Payments', 'wp-backend-dash'),
    [WBEOrdersController::init(), 'index'],
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
    5,
    false
);