<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{

    protected $table = 'chat';
    
    protected $fillable = [
        'username',
        'avatar',
        'message',
        'room',
        'time'
    ];

    protected $hidden = [
        'created_at'
    ];
}
