<?php

namespace WPBackendDash\Web;

use WPBackendDash\Helpers\WBEAPIManager;
use WPBackendDash\Controllers\WBEApiController;

$apiManager = new WBEAPIManager("wbe/v1");

$apiManager->add_route('/chat/get_chat_create', 'GET', [WBEApiController::class, 'getChatCreateModal'], [], WBEAPIManager::require_login());