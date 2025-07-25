<?php

namespace WPBackendDash\Controllers;

use WPBackendDash\Helpers\ControllerHelper;
use WPBackendDash\Helpers\WBERequest;

class WBEApiController extends ControllerHelper {
    

    public function getChatCreateModal()
    {
        // Lógica para crear un modal de chat
        return self::view('api/chat_create_modal');
    }
}