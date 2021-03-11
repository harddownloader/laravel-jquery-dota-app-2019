<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bots extends Model
{

    protected $table = 'bots';
    
    protected $fillable = [
        'steamid64',
        'username',
        'password',
        'shared_secret',
        'identity_secret',
        'trade',
        'online'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
