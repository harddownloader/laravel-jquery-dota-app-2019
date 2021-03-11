<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Double extends Model
{

    protected $table = 'double';
    
    protected $fillable = [
        'id',
        'random',
        'price',
        'color',
        'number',
        'status'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
