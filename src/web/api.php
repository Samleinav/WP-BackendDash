<?php

namespace WPBackendDash\Web;

use WPBackendDash\Helpers\WBEAPIManager;
use WPBackendDash\Controllers\WBEApiController;

$apiManager = new WBEAPIManager("wbe/v1");
$apiController = new WBEApiController();
$apiManager->get('/chat/get_chat_create', [$apiController, 'getChatCreateModal'], [], WBEAPIManager::require_login());