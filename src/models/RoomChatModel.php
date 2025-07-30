<?php

namespace WPBackendDash\Models;
use WPBackendDash\Helpers\WBEModelBase;

class RoomChatModel  extends WBEModelBase {

    protected $table = 'ai_interviews';
    protected $fillable = [
    'user_id', 
    'meeting_link', 
    'type', 
    'details',
    'token',
    'attachments',
    'time',
    'tokens',
    'interview_complete',
    'in_use'];

    protected $required = [
        'meeting_link', 
        'type', 
        'details',
        'time'
    ];

}
