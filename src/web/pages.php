<?php
namespace WPBackendDash\Web;

use WPBackendDash\Helpers\WBEPage;
use WPBackendDash\Controllers\WBEOrdersController;

WBEPage::add(
    'wbe_admin_page_orders',
    __('Payments', 'wp-backend-dash'),
    [WBEOrdersController::init(), 'index'],
    'dashicons-admin-generic',
    'wbe_view_orders',
    4,
    true
);

WBEPage::add(
    'wbe_admin_page_order_view',
     __('Order Details', 'wp-backend-dash'),
    [WBEOrdersController::init(), 'order_view'],
    'dashicons-admin-generic',
    'wbe_view_order_details',
    5,
    false
);