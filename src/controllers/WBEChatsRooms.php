<?php
namespace WPBackendDash\Controllers;
use WPBackendDash\Helpers\WBERequest;
use WPBackendDash\Helpers\ControllerHelper;
use WPBackendDash\Helpers\WBEForm;
use WPBackendDash\Models\RoomChatModel;

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

    public function store() {
        // Lógica para almacenar una nueva sala de chat
        $data = WBERequest::all();
        
        // Validación y almacenamiento de datos
        $roomChat = new RoomChatModel();
        $roomChat->fill($data);
        if ($roomChat->save()) {
            return $request->Response()
            ->addAction("wbeShowNotify", ["Sala de chat creada exitosamente.", "success"])
            ->addAction("wbeRedirect", ["url" => wberoute('center.rooms.index')])
            ->send();
        } else {
            return $request->Response()
                ->addAction("wbeShowNotify", ["Error al crear la sala de chat.", "error"]) 
                ->send();
        }
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