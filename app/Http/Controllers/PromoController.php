<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use App\Promo;
use Illuminate\Http\Request;
use Auth;
use Session;
use Redis;
use App\Settings;

class PromoController extends Controller
{
    public function __construct()
    {
         if(Auth::check()) {
             $this->user = Auth::user();
             view()->share('u', $this->user);
         }

        $this->lang = Parent::getLang();

        $this->redis = Redis::connection();

        $this->config = Settings::first();
    }

    public function test()
    {
        // $users = User::get();
        // foreach($users as $user) {
        //     $user->ref = $this->getRef();
        //     $user->save();
        // }
        // return 'success';
        for($i = 0; $i < 1; $i++) {
            DB::table('double_bets')->insert([
                'user_id' => 501,
                'game_id' => $i+1,
                'username' => 'P4R4P3T',
                'avatar' => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/91/91d475c55938cc927940b80afb6ae8a30aa423d6_full.jpg',
                'type' => 'blue',
                'value' => 300
             ]);
        }
        return 'success';
    }

    public function getRef()
    {
        $str = '';
        for($i = 0; $i < 9; $i++)
        {
            $keys = str_shuffle('ABCDEFGHIGKLMONPQRSTYVWXYZ123456789');
            $str .= $keys[mt_rand(0, count($keys)-1)];
        }
        $test = User::where('ref', $str)->first();
        if(is_null($test)) return $str;
        return $this->getRef();  
    }

    public function index()
    {
        if(Auth::guest()) return view('errors.404');
        $list = Promo::orderBy('id', 'asc')->get();
        parent::setTitle('Промокоды');

        foreach($list as $i => $promo) $list[$i]->used = DB::table('promo_list')->where('promo_id', $promo->id)->count();

        return view('admin.promo', compact('list'));
    }

    public function edit($id)
    {
        if(Auth::guest()) return view('errors.404');
        $promo = Promo::where('id', $id)->first();
        parent::setTitle('Редактирование промокода #'.$id);
        return view('admin.edit_promo', compact('promo'));
    }

    public function createNewPromo(Request $r)
    {
        if(Auth::guest() || $this->user->permission < 2) return [
            'success' => false,
            'msg' => 'Permission denied'
        ];

        $l = Promo::where('promo', $r->get('promo'))->first();
        if(!is_null($l)) return [
            'success' => false,
            'msg' => 'Такой промокод уже существует!'
        ];

        if(floatval($r->get('price')) < 1) return [
            'success' => false,
            'msg' => 'Цена должна быть больше нуля!'
        ];

        Promo::create([
            'promo' => $r->get('promo'),
            'count' => floatval($r->get('count')),
            'money' => floatval($r->get('price'))
        ]);

        return [
            'success' => true,
            'msg' => 'Промокод "'.$r->get('promo').'" успешно зарегистрирован!'
        ];
    }

    public function deletePromo($id)
    {
        if(Auth::guest() || $this->user->permission < 2) return [
            'success' => false,
            'msg' => 'Permission denied'
        ];

        $l = Promo::where('id', $id)->first();
        if(is_null($l)) return [
            'success' => false,
            'msg' => 'Такой промокод не существует!'
        ];

        Promo::where('id', $id)->delete();

        return redirect()->back();
    }

    public function savePromo(Request $r)
    {
        if(Auth::guest() || $this->user->permission < 2) return [
            'success' => false,
            'msg' => 'Permission denied'
        ];

        if(floatval($r->get('price')) < 1) return [
            'success' => false,
            'msg' => 'Цена должна быть больше нуля!'
        ];

        Promo::where('id', $r->get('id'))->update([
            'promo' => $r->get('promo'),
            'count' => floatval($r->get('count')),
            'money' => floatval($r->get('price'))
        ]);

        return [
            'success' => true,
            'msg' => 'Промокод "'.$r->get('promo').'" успешно сохранен!'
        ];
    }

    public function redeemPromo(Request $r)
    {
        if(Auth::guest()) return [
            'success' => false,
            'msg' => 'Permission denied'
        ];
        
        $user = User::where('id', $this->user->id)->first();
        $type = 0;
        
        $promo = Promo::where('promo', $r->get('promo'))->first();
        if(is_null($promo))
        {
            // Реферальный код
            $owner = User::where('ref', $r->get('promo'))->first();
            if(is_null($owner)) return [
                'success' => false,
                'msg' => 'This code does not exist!'
            ];
            
            if($user->is_ref) return [
                'success' => false,
                'msg' => 'You have already entered a referral code!'
            ];
            
            if($owner->id == $user->id) return [
                'success' => false,
                'msg' => 'You can not enter your referral code!'
            ];
            
            $count = User::where('my_ref', $owner->ref)->count();
            if($count >= $this->config->ref_count) return [
                'success' => false,
                'msg' => 'This user has the maximum number of referrals!'
            ];
            
            $type = 1;
        } else {
            if(Promo::check($user->id, $promo->id)) return [
                'success' => false,
                'msg' => 'You have already activated this code!'
            ];

            if(Promo::getCount($promo->id) == $promo->count) return [
                'success' => false,
                'msg' => 'This promotional code has expired!'
            ];
            
            $type = 0;
        }
        
        DB::table('promo_queue')->insert([
            'user_id' => $this->user->id,
            'promo' => $r->get('promo'),
            'type' => $type
        ]);
        
        $this->redis->publish('promo.queue', json_encode([
            'user_id' => $this->user->id
        ]));
    }
    
    public function checkQueue(Request $r)
    {
        $user = User::where('id', $r->get('user_id'))->first();
        $queue = DB::table('promo_queue')->where('user_id', $r->get('user_id'))->first();
        if(is_null($queue)) 
        {
            // Очищаем бд
            DB::table('promo_queue')->where('user_id', $r->get('user_id'))->delete();
             
            // Оповещаем юзера
            $this->redis->publish('message', json_encode([
                'user_id' => $r->get('user_id'),
                'msg' => 'We could not find you in the queue!',
                'type' => 'error'
            ]));
    
            return;
        }
        
        
        if($queue->type == 0)
        {
            $promo = Promo::where('promo', $queue->promo)->first();
            if(is_null($promo))
            {  
                $this->redis->publish('message', json_encode([
                    'user_id' => $user->id,
                    'msg' => 'This code does not exist!',
                    'type' => 'error'
                ]));

                // Очищаем бд
                DB::table('promo_queue')->where('user_id', $r->get('user_id'))->delete();
                
                return;
            }
        
            Promo::addList([
                'user_id' => $user->id,
                'promo_id' => $promo->id
            ]);

            $user->money += $promo->money;
            $user->save();

            AchievementController::checkAchievement($user, $this->redis, $this->lang['achievement_unlock']);

            parent::updateBalance($user->steamid64);

            $this->redis->publish('message', json_encode([
                'user_id' => $user->id,
                'msg' => 'Promotional code "'.$promo->promo.'" successfully activated. Your balance has been added '.$promo->money.' coins!',
                'type' => 'success'
            ]));
        } else {
            $owner = User::where('ref', $queue->promo)->first();
            if(is_null($owner)) 
            {
                $this->redis->publish('message', json_encode([
                    'user_id' => $user->id,
                    'msg' => 'This code does not exist!',
                    'type' => 'error'
                ]));

                // Очищаем бд
                DB::table('promo_queue')->where('user_id', $r->get('user_id'))->delete();
                
                return;
            }
            
            if($user->is_ref)
            {
                $this->redis->publish('message', json_encode([
                    'user_id' => $user->id,
                    'msg' => 'You have already entered a referral code!',
                    'type' => 'error'
                ]));

                // Очищаем бд
                DB::table('promo_queue')->where('user_id', $r->get('user_id'))->delete();
                
                return;
            }
            
            $user->is_ref = 1;
            $user->my_ref = trim(mb_strtoupper($queue->promo));
            $user->money += $this->config->ref_rem_money;
            $user->save();
            
            $owner->money += $this->config->ref_own_money;
            $owner->save();

            $this->redis->publish('message', json_encode([
                'user_id' => $owner->id,
                'msg' => $user->username.' became your referral!',
                'type' => 'info'
            ]));

            $this->redis->publish('message', json_encode([
                'user_id' => $user->id,
                'msg' => 'You have successfully become a referral '.$owner->username,
                'type' => 'info'
            ]));

            AchievementController::checkAchievement($user, $this->redis, $this->lang['achievement_unlock']);
        
            parent::updateBalance($user->steamid64);
        }
        
        // Очищаем бд
        DB::table('promo_queue')->where('user_id', $r->get('user_id'))->delete();
        
        return;
    }

    public static function ref($summ, $user)
    {
        $redis = \Redis::connection();
        $config = \App\Settings::first();

        $summ = floor(($summ/100)*$config->ref_percent);
        if($summ == 0) return;

        $owner = \App\User::where('ref', $user->my_ref)->first();
        if(is_null($owner)) return;

        $owner->money += $summ;
        $owner->save();

        // parent::updateBalance($owner->steamid64);

        $redis->publish('updateBalance', json_encode([
            'user_id' => $owner->id,
            'balance' => number_format($owner->money, 0, ' ', ' ')
        ]));

        $redis->publish('message', json_encode([
            'user_id' => $owner->id,
            'msg' => $user->username.' brought you '.$summ,
            'type' => 'info'
        ]));
    }
}
