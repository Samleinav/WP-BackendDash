<?php

namespace WPBackendDash\Web;

use WPBackendDash\Helpers\WBEAPIManager;
use WPBackendDash\Controllers\WBEApiController;
use WPBackendDash\Controllers\WBEChatsRooms;

WBEAPIManager::get("chat.getform",'/chat/get_chat_create', [WBEApiController::class, 'getChatCreateModal'], [], WBEAPIManager::require_login());
WBEAPIManager::post("chat.create", '/chat/create', [WBEChatsRooms::class, 'store'], [], WBEAPIManager::require_login());