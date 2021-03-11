<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{

    protected $table = 'settings';
    
    protected $fillable = [
        'sitename',
        'descriptions',
        'keywords'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
