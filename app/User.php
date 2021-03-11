<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;
use Redis;
use Carbon\Carbon;

class User extends Authenticatable
{

    protected $table = 'users';

    protected $fillable = [
        'username',
        'avatar',
        'steamid64',
        'flagState',
        'money',
        'is_banned',
        'id',
        'ref',
        'achievements'
    ];

    protected $hidden = [
        'remember_token',
        'created_at',
        'updated_at'
    ];

    public static function addXp($id, $xp)
    {
        $user = DB::table('users')->where('id', $id)->first();
        if(is_null($user)) return;

        if($user->today < Carbon::today()->format('Y-m-d H:00:00')) 
        {
            $user->today = Carbon::today()->format('Y-m-d H:00:00');
            $user->today_lvls = 0;
        }

        $lvl = $user->lvl;
        $xp = $user->xp + $xp;
        while($xp >= $user->n_xp) {
            $xp -= $user->n_xp;
            $lvl++;
            $user->n_xp = $user->n_xp + floor(($user->n_xp/100)*75);
        }

        $user->today_lvls += $lvl-$user->lvl;

        if($user->lvl < $lvl) {
            $redis = Redis::connection();
            $redis->publish('lvlup', json_encode([
                'user_id' => $user->id,
                'lvl' => $lvl
            ]));
        }

        DB::table('users')->where('id', $id)->update([
            'xp' => $xp,
            'lvl' => $lvl,
            'n_xp' => $user->n_xp,
            'today' => $user->today,
            'today_lvls' => $user->today_lvls
        ]);
    }
}
