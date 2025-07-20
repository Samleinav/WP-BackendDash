<?php
namespace WPBackendDash\Controllers;
use WPBackendDash\Helpers\WBERequest;
use WPBackendDash\Helpers\ControllerHelper;

class WBEChatsRooms extends ControllerHelper {
    public static function init() {
        // Aquí puedes inicializar cualquier cosa que necesites para el controlador
        return new self();
    }

    public function index() {
        // Lógica para mostrar la lista de salas de chat
        echo WBERequest::fullUrl() . "<br>";
    }

    public function room_view($room_id) {
        // Lógica para mostrar los detalles de una sala de chat específica
        echo "Detalles de la sala de chat: " . esc_html($room_id);
    }
}