<?php

namespace WPBackendDash\Web;

use WPBackendDash\Helpers\WBEAPIManager;
use WPBackendDash\Controllers\WBEApiController;

$apiManager = new WBEAPIManager("wbe/v1");

$apiManager->get('/chat/get_chat_create', [WBEApiController::class, 'getChatCreateModal'], [], WBEAPIManager::require_login());