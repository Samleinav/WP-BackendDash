<?php

namespace WPBackendDash\Web;

use WPBackendDash\Helpers\WBEAPIManager;
use WPBackendDash\Controllers\WBEApiController;
use WPBackendDash\Controllers\WBEChatsRooms;

// WBEAPIManager::setNamespace('wbe/v1'); can be set in the main plugin file or wherever appropriate
// final /wp-json/wbe/v1/

WBEAPIManager::get("chat.getform",'/chat/get_chat_create', [WBEApiController::class, 'getChatCreateModal'], [], WBEAPIManager::require_login());
WBEAPIManager::post("chat.create", '/chat/create', [WBEChatsRooms::class, 'store'], [], WBEAPIManager::require_login());
WBEAPIManager::post("chat.update", '/chat/{token}/update', [WBEChatsRooms::class, 'update'], [], WBEAPIManager::require_login());