<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{

    protected $table = 'shop';

    protected $fillable = [
        'bot_id',
        'market_hash_name',
        'icon_url',
        'classid',
        'assetid',
        'type',
        'quality',
        'hero',
        'rarity',
        'price'
    ];

    protected $hidden = [
        'created_at'
    ];
}
