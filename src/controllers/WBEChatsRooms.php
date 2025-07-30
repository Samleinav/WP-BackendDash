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

        WBERequest::validate(new RoomChatModel());

        $request = WBERequest::request();

        $data = $request->all();

        // Validación y almacenamiento de datos
        $roomChat = new RoomChatModel();
        $roomChat->fill($data);
        $roomChat->user_id = get_current_user_id(); // Asignar el ID del usuario actual
        $roomChat->token = wp_generate_uuid4(); // Generar un token único

        if($request->hasFile('attachments')) {
            $attachments = $request->file('attachments');
            $file = $this->uploadFile($attachments, ['user_id' => $roomChat->user_id]);
            $roomChat->attachments = $file['file_id'] ?? null;
        }

         // Asignar el ID del archivo adjunto si se subió uno

        if ($roomChat->save()) {
            $response = WBERequest::Response();

            $response->addAction("wbeShowNotify", ["Exito!", "Sala de chat creada exitosamente.", "success"]);

            return $response->addAction("wbeRedirect", [ wberoute('center.rooms.index'), $force = true ])
            ->wpjson();
        } else {
            return $this->response()
                ->addAction("wbeShowNotify", ["Error al crear la sala de chat.", "error"]) 
                ->wpjson();
        }
    }

    public function edit($room_id) {
        // Lógica para editar una sala de chat existente
        $roomChat = RoomChatModel::where("token", $room_id);

        if (!$roomChat){
            return "No found";
        }
        $roomChat = $roomChat[0];

        return self::view('chats_rooms/edit', compact('roomChat'));
    }

    public function update(WBERequest $request, $token) {
        // Lógica para actualizar una sala de chat existente)
        $roomChat = RoomChatModel::where("token", $token);

        if (!$roomChat){
            return "No found";
        }
        $roomChat = $roomChat[0];

        $data = $request->all();

        $roomChat->fill($data);
        
        if($request->hasFile('attachments')) {
            $attachments = $request->file('attachments');
            $file = $this->uploadFile($attachments, ['user_id' => $roomChat->user_id]);
            $roomChat->attachments = $file['file_id'] ?? null;
        }

        $roomChat->in_use = isset($data['in_use']) ? $data['in_use'] : 0;
        $roomChat->interview_complete = isset($data['interview_complete']) ? $data['interview_complete'] : 0;


        if ($roomChat->save()) {
            $response = WBERequest::Response();

            $response->addAction("wbeShowNotify", ["Exito!", "Sala de chat creada exitosamente.", "success"]);

            return $response->addAction("wbeRedirect", [ wberoute('center.rooms.index'), $force = true ])
            ->wpjson();
        } else {
            return $this->response()
                ->addAction("wbeShowNotify", ["Error al crear la sala de chat.", "error"]) 
                ->wpjson();
        }


    }                                                     

    public function delete($room_id) {
        // Lógica para eliminar una sala de chat
        echo "Sala de chat eliminada: " . esc_html($room_id);
    }
}