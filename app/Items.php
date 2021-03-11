<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Items extends Model
{

    protected $table = 'items';
    
    protected $fillable = [
        'market_hash_name',
        'price'
    ];

    protected $hidden = [
        'created_at'
    ];
}
