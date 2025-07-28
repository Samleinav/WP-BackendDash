<?php

namespace WPBackendDash\Models;
use WPBackendDash\Helpers\WBEModelBase;

class RoomChatModel  extends WBEModelBase {

    protected $table = 'room_chats';
    protected $fillable = ['user_id', 'meeting_link', 'type', 'details', 'attachments', 'time', 'tokens', 'interview_complete', 'in_use'];


}
