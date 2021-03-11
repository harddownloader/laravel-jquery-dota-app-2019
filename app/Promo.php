<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Promo extends Model
{

    protected $table = 'promo';

    protected $fillable = [
        'promo',
        'money',
        'count'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public static function check($userID, $promoID)
    {
        $l = DB::table('promo_list')->where('user_id', $userID)->where('promo_id', $promoID)->first();
        if(is_null($l)) return false;
        return true;
    }

    public static function getCount($promoID)
    {
        $l = DB::table('promo_list')->where('promo_id', $promoID)->count();
        return $l;
    }

    public static function addList($array)
    {
        DB::table('promo_list')->insert($array);
    }
}
