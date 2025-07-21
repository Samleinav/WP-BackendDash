<?php
namespace WPBackendDash\Controllers;
use WPBackendDash\Helpers\WBERequest;
use WPBackendDash\Helpers\ControllerHelper;

class WBEChatsRooms extends ControllerHelper {
    public static function init() {
        // Aquí puedes inicializar cualquier cosa que necesites para el controlador
        return new self();
    }

    public function index($custom_order_serial) {
        // Lógica para mostrar la lista de salas de chat
        echo WBERequest::fullUrl() . "<br>" . "custom_order_serial ID: " . esc_html($custom_order_serial);
    }

    public function room_view($room_id) {
        // Lógica para mostrar los detalles de una sala de chat específica
        echo "Detalles de la sala de chat: " . esc_html($room_id);
    }

    public function create() {
        // Lógica para crear una nueva sala de chat
        echo "Formulario para crear una nueva sala de chat";
    }

    public function edit($room_id) {
        // Lógica para editar una sala de chat existente
        echo "Formulario para editar la sala de chat: " . esc_html($room_id);
    }

    public function delete($room_id) {
        // Lógica para eliminar una sala de chat
        echo "Sala de chat eliminada: " . esc_html($room_id);
    }
}