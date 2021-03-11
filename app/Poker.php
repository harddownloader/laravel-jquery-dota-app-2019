<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Poker extends Model
{

    protected $table = 'poker';

    protected $fillable = [
        'user_id',
        'cards',
        'trips',
        'ante',
        'blind',
        'preflop',
        'postflop',
        'preshow',
        'status'
    ];

    protected $hidden = [
        'updated_at'
    ];
}
