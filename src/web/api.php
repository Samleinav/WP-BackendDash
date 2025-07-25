<?php

namespace WPBackendDash\Web;

use WPBackendDash\Helpers\WBEAPIManager;
use WPBackendDash\Controllers\WBEApiController;

$apiController = new WBEApiController();
WBEAPIManager::get('/chat/get_chat_create', [$apiController, 'getChatCreateModal'], [], WBEAPIManager::require_login());