<?php
namespace WPBackendDash\Controllers;
use WPBackendDash\Helpers\WBERequest;
use WPBackendDash\Helpers\ControllerHelper;
use WPBackendDash\Helpers\WBEForm;

class WBEChatsRooms extends ControllerHelper {
 

    public function index() {
        // Lógica para mostrar la lista de salas de chat
        return self::view('chats_rooms/index');
    }

    public function room_view($room_id) {
        // Lógica para mostrar los detalles de una sala de chat específica
        return self::view('chats_rooms/view', compact('room_id'));
    }

    public function create() {
         
        WBEForm::bootstrap();
        // Lógica para crear una nueva sala de chat
        return self::view('chats_rooms/create');
    }

    public function edit($room_id) {
        // Lógica para editar una sala de chat existente
        return self::view('chats_rooms/edit', compact('room_id'));
    }

    public function delete($room_id) {
        // Lógica para eliminar una sala de chat
        echo "Sala de chat eliminada: " . esc_html($room_id);
    }
}