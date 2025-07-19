<?php

namespace WPBackendDash\Web;

use WPBackendDash\Helpers\WBEPage;

use WPBackendDash\Controllers\WBEOrdersController;

//require_once WBE_PLUGIN_PATH. 'includes/src/Controllers/WBEOrdersController.php';

WBEPage::add(
    'wbe_admin_page_testpage',
    'Test Page',
    [WBEOrdersController::init(), 'index'],
    'dashicons-admin-generic',
    'manage_options',
    100,
    false
);
