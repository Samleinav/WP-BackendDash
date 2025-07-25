<?php

namespace WPBackendDash\Controllers;

use WPBackendDash\Helpers\ControllerHelper;
use WPBackendDash\Helpers\WBERequest;

class WBEApiController extends ControllerHelper {
    
    /**
     * Método para obtener el modal de creación de chat
     */
    public function getChatCreateModal( $request )
    {
        // Lógica para crear un modal de chat
        return self::view('chats_rooms/create');
    }
}