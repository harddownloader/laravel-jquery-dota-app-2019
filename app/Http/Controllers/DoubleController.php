<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use App\Chat;
use App\Settings;
use App\Double;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Redis;
use Auth;

class DoubleController extends Controller
{

    public function __construct()
    {
        $this->redis  = Redis::connection();
        $this->game   = Double::orderBy('id', 'desc')->first();
        $this->rotate = $this->redis->get('rotate');
        $this->config = Settings::first();
        view()->share('config', $this->config);
        view()->share('rotate', $this->rotate);
        if(Auth::check()) {
            $this->user = Auth::user();
            view()->share('u', Auth::user());
        }

        $this->lang = Parent::getLang();
        view()->share('lang', $this->lang);
    }

    public function index()
    {
        Controller::setTitle($this->lang['roulette']['title']);
        $game   = $this->game;
        $bets   = $this->getGameBets();
        // $bets = []
        // $prices = $this->getColorPrices();
        $time   = $this->config->double_timer;

        #chat
        $chat   = $this->getMessages();
        // $chat = [];
        $top    = $this->getTopBets();

        return view('pages.double', compact('game', 'bets', 'prices', 'time', 'chat', 'top'));
    }

    public function getMessages()
    {
        // return [];
        $messages = DB::table('chat')->where('room', 'double')->limit(20)->orderBy('id', 'desc')->get();
        $messges = array_reverse($messages);
        $list = [];

        foreach($messages as $message) {

            if($message->is_fake) $user = DB::table('users_fake')->where('id', $message->user_id)->first();
            else $user = User::where('id', $message->user_id)->first();

            if(!is_null($user)) $list[] = [
                'username' => $user->username,
                'avatar' => $user->avatar,
                'message' => $message->message,
                'room' => $message->room,
                'lvl' => $user->lvl,
                'time' => $message->time
            ];
        }

        return array_reverse($list);
    }

    public function getGameBets()
    {
        $bets = DB::table('double_bets')
            ->where('game_id', $this->game->id)
            ->orderBy('value', 'desc')
            ->get();

        foreach($bets as $i => $bet) if($bet->value >= 1000) $bets[$i]->value = number_format($bet->value/1000,1).'K';

        return $bets;
    }

    public function getTopBets()
    {
        // $list = [
        //     'blue' => [
        //         'count' => 0,
        //         'value' => 0
        //     ],
        //     'green' => [
        //         'count' => 0,
        //         'value' => 0
        //     ],
        //     'yellow' => [
        //         'count' => 0,
        //         'value' => 0
        //     ],
        //     'red' => [
        //         'count' => 0,
        //         'value' => 0
        //     ]
        // ];

        $list = [
            'blue' => [
                'count' => count(DB::table('double_bets')->where('game_id', $this->game->id)->where('type', 'blue')->groupBy('user_id')->get()),
                'value' => DB::table('double_bets')->where('game_id', $this->game->id)->where('type', 'blue')->sum('value')
            ],
            'green' => [
                'count' => count(DB::table('double_bets')->where('game_id', $this->game->id)->where('type', 'green')->groupBy('user_id')->get()),
                'value' => DB::table('double_bets')->where('game_id', $this->game->id)->where('type', 'green')->sum('value')
            ],
            'yellow' => [
                'count' => count(DB::table('double_bets')->where('game_id', $this->game->id)->where('type', 'yellow')->groupBy('user_id')->get()),
                'value' => DB::table('double_bets')->where('game_id', $this->game->id)->where('type', 'yellow')->sum('value')
            ],
            'red' => [
                'count' => count(DB::table('double_bets')->where('game_id', $this->game->id)->where('type', 'red')->groupBy('user_id')->get()),
                'value' => DB::table('double_bets')->where('game_id', $this->game->id)->where('type', 'red')->sum('value')
            ]
        ];

        foreach($list as $key => $b) if($b['value'] >= 1000) $list[$key]['value'] = round($b['value'] / 1000, 1).'K';

        return $list;
    }

    public function getColorPrices()
    {
        return [
            'red' => DB::table('double_bets')->where('game_id', $this->game->id)->where('type', 'red')->sum('value'),
            'green' => DB::table('double_bets')->where('game_id', $this->game->id)->where('type', 'green')->sum('value'),
            'black' => DB::table('double_bets')->where('game_id', $this->game->id)->where('type', 'black')->sum('value')
        ];
    }

    public function addBet(Request $r)
    {
        if(Auth::guest()) return ['success' => false];
        
        $user = User::where('id', $this->user->id)->first();
        
        if($this->game->status > 1) return [
            'success' => false,
            'msg' => $this->lang['roulette']['bets_was_closed']
        ];
        
        if(floor($r->get('value')) < 1) return [
            'success' => false,
            'msg' => 'You forgot to specify the amount of the bet!'
        ];
        
        if(!in_array($r->get('type'), ['blue', 'green', 'yellow', 'red'])) return [
            'success' => false,
            'msg' => $this->lang['roulette']['dunderstand']
        ];
    
        $username = $user->username;
        if(iconv_strlen($username) > 8) $username = substr($username, 0, 8).'...';
        
        // Insert
         try{

            DB::beginTransaction();

        DB::table('double_bets_queue')->insert([
            'user_id' => $user->id,
            'username' => $username,
            'avatar' => $user->avatar,
            'game_id' => $this->game->id,
            'type' => $r->get('type'),
            'value' => floor($r->get('value')),
            'balance' => $user->money
        ]);
        
        // Check Queue
        $this->redis->publish('double.queue', json_encode([
            'user_id' => $user->id
        ]));

		DB::commit();

        } catch(\Exception $exception){

            DB::rollBack();

            // Allow Laravel engine to handle this exception
            throw $exception;
        }
		
        return [
            'success' => true,
            'msg' => $this->lang['roulette']['bet_queue']
        ];
    }
    
    public function checkQueue(Request $r)
    {
        $bet = DB::table('double_bets_queue')->where('user_id', $r->get('user_id'))->where('game_id', $this->game->id)->first();
        if(is_null($bet))
        {
            // Удаляем все ставки игрока, дабы бд не забивалась.
            DB::table('double_bets_queue')->where('user_id', $r->get('user_id'))->delete();
            
            // Оповещаем об этом юзера.
            $this->redis->publish('message', json_encode([
                'user_id' => $r->get('user_id'),
                'msg' => $this->lang['roulette']['queue_is_clear'],
                'type' => 'error'
            ]));
            
            return ['success' => true]; // Response
        }
        
        $user = User::where('id', $bet->user_id)->first();
        if($bet->value > $user->money) {
            // Удаляем все ставки игрока, дабы бд не забивалась.
            DB::table('double_bets_queue')->where('user_id', $r->get('user_id'))->delete();
            
            // Оповещаем об этом юзера.
            $this->redis->publish('message', json_encode([
                'user_id' => $r->get('user_id'),
                'msg' => $this->lang['roulette']['small_money'],
                'type' => 'error'
            ]));
            
            return ['success' => true]; // Response
        }
        
        // Проверяем, пользователя на кол-во множителей
        $colors = DB::table('double_bets')->where('user_id', $r->get('user_id'))->where('game_id', $bet->game_id)->where('is_fake', 0)->groupBy('type')->get();
        
        $array = [];
        foreach($colors as $color) if(!in_array($color->type, $array)) $array[] = $color->type;
        
        if(count($array) >= $this->config->double_candoit && !in_array($bet->type, $array)) {
    
            // Оповещаем юзера о максимальном кол-ве допустимых множителей, если оно превышено.        
            $this->redis->publish('message', json_encode([
                'user_id' => $r->get('user_id'),
                'msg' => $this->lang['roulette']['m_counts'].$this->config->double_candoit,
                'type' => 'error'
            ])); 
            
            // Удаляем все ставки из очереди, дабы бд не забивалась.
            DB::table('double_bets_queue')->where('user_id', $r->get('user_id'))->delete();
            
            return;
        }
        
        // Insert
        DB::table('double_bets')->insert([
            'user_id' => $bet->user_id,
            'username' => $bet->username,
            'avatar' => $bet->avatar,
            'game_id' => $bet->game_id,
            'value' => $bet->value,
            'type' => $bet->type,
            'balance' => $bet->balance
        ]);
        // Удаляем все ставки из очереди.
        DB::table('double_bets_queue')->where('user_id', $r->get('user_id'))->delete();
        
        $user->money -= $bet->value;
        $user->save();
        
        // Выводим ставку на игровое поле
        $this->redis->publish('double.new.bet', json_encode([
            'bet' => [
                'username' => $bet->username,
                'value' => $bet->value,
                'type' => $bet->type
            ],
            'top' => $this->getTopBets()
        ]));
        
        // Обновляем баланс пользователя
        parent::updateBalance($user->steamid64);
        
        // Добавляем опыт
        User::addXp($user->id, $bet->value);
        
        $this->redis->publish('message', json_encode([
            'user_id' => $r->get('user_id'),
            'msg' => $this->lang['roulette']['bet_accept'],
            'type' => 'success'
        ]));
    }

    public function checkTimer()
    {
        if($this->game->status > 0) return;

        $users = count(DB::table('double_bets')
            ->where('game_id', $this->game->id)
            ->groupBy('user_id')
            ->get());

        if(($this->config->double_minplayers != 0) && ($users < $this->config->double_minplayers)) return;

        $this->game->status = 1;
        $this->game->save();

        $returnValue = [
            'time'  => $this->config->double_timer,
            'id'    => $this->game->id
        ];

        $this->redis->publish('double.timer', json_encode($returnValue));
    }

    public function getSlider()
    {
        $profit = $this->getProfit();

        $list = [
            [4, 15, 'blue', 2],
            [18, 30, 'green', 3],
            [34, 104, 'blue', 2],
            [109, 120, 'blue', 2],
            [125, 135, 'blue', 2],
            [139, 150, 'blue', 2],
            [155, 165, 'green', 3],
            [170, 180, 'yellow', 5],
            [185, 225, 'blue', 2],
            [230, 240, 'blue', 2],
            [244, 284, 'green', 3],
            [290, 301, 'red', 10],
            [305, 344, 'yellow', 5],
            [350, 361, 'green', 3]


        ];

        // shuffle($list);

        $key  = floor(count($list)*$this->game->random);
        if(!isset($list[$key])) $key--;
        $data = $list[$key];

        if($profit) {
            $cList = [];
            foreach($list as $item) if($item[2] == $profit) $cList[] = $item;
            $data = $cList[mt_rand(0, count($cList)-1)];
        }

        $this->game->status     = 2;
        $this->game->color      = $data[2];
        $this->game->multiplier = $data[3];
        // $this->game->profit = $profit['profit'];
        $this->game->save();

        if($this->rotate < 0) $this->rotate = 0;

        $rotate = 360-($this->rotate-((floor($this->rotate/360))*360))+720+$this->rotate+mt_rand($data[0], $data[1]);

        $returnValue = [
            'rotate_now' => $this->rotate,
            'rotate'     => $rotate,
            'id'         => $this->game->id,
            'time'       => [
                'slider'      => $this->config->double_timetoslider,
                'newgame'     => $this->config->double_timetonewgame
            ],
            'type' => $data[2]
        ];

        // FIXME:
        $users = DB::table('double_bets')->where('game_id', $this->game->id)->where('is_fake', 0)->groupBy('user_id')->get();
        if(!is_null($this->game->color)) {
            foreach($users as $i => $user) {
                $user = User::where('id', $user->id)->first();
                if(!is_null($user))
                {
                    if(!is_null(DB::table('double_bets')->where('game_id', $this->game->id)->where('user_id', $user->user_id)->where('type', $this->game->color)->first())) {
                        $this->redis->publish('double.result', json_encode([
                            'user_id' => $user->user_id,
                            'result' => true,
                            'time' => $this->config->double_timetoslider
                        ]));
                    } else {
                        $this->redis->publish('double.result', json_encode([
                            'user_id' => $user->user_id,
                            'result' => false,
                            'time' => $this->config->double_timetoslider
                        ]));
                    }
                }
            }
        }

        $this->redis->publish('double.show.random', json_encode(['random' => $this->game->random, 'time' => $this->config->double_timetoslider]));

        return response()->json($returnValue);
    }

    public function getStatus()
    {
        $returnValue = [
            'status'    => $this->game->status,
            'id'        => $this->game->id,
            'time'      => $this->config->double_timer
        ];
        return response()->json($returnValue);
    }

    public function newGame()
    {
        $this->game->status = 3;
        $this->game->save();
        
        DB::table('users')->update(['is_bet' => 0]);

        $users = DB::table('double_bets')
            ->where('game_id', $this->game->id)
            ->where('type', $this->game->color)
            ->where('is_fake', 0)
            ->select('user_id', 'type', DB::raw('SUM(value) as value'))
            ->groupBy('user_id')
            ->get();

        foreach($users as $user) {
            $u = User::where('id', $user->user_id)->first();
            if(!is_null($u))
            {
                $u->money += floor($user->value*$this->game->multiplier);
                $u->save();
                parent::updateBalance($u->steamid64);
                PromoController::ref(floor($user->value*$this->game->multiplier), $u);
                AchievementController::checkAchievement($u, $this->redis, $this->lang['achievement_unlock']);
            }
        }

        $users = DB::table('double_bets')->where('game_id', $this->game->id)->where('is_fake', 0)->groupBy('user_id')->get();
        foreach($users as $user)
        {
            $u = User::where('id', $user->user_id)->first();
            if(!is_null($u))
            {
                parent::updateBalance($u->steamid64);
                AchievementController::checkAchievement($u, $this->redis, $this->lang['achievement_unlock']);
            }
        }

        #

        $bets = DB::table('double_bets')
            ->where('game_id', $this->game->id)
            ->where('type', $this->game->color)
            ->get();

        foreach($bets as $bet) DB::table('double_bets')->where('id', $bet->id)->update(['is_win' => 1, 'winner_value' => $bet->value*$this->game->multiplier]);

        # Create New Game
        Double::insert([
            'random' => '0.'.mt_rand(1000000,9999999).mt_rand(1000000,9999999).mt_rand(1000000,9999999)
        ]);

        DB::table('users_fake')->update(['add_bet' => 0]);

        $this->game = Double::orderBy('id', 'desc')->first();

        // Double::where('id', '!=', $this->game->id)->delete();
        // DB::table('double_bets')->where('game_id', '!=', $this->game->id)->delete();

        $this->rotate = $this->rotate-(floor($this->rotate/360)*360);
        $this->redis->set('rotate', $this->rotate);

        $returnValue = [
            'id'    => $this->game->id,
            'hash'  => hash('sha1', $this->game->random),
            'time'  => $this->config->double_timer,
            'rotate' => $this->rotate
        ];

        $this->redis->publish('double.new.game', json_encode($returnValue));

        return response()->json($returnValue);
    }

    public function fakeBets()
    {
        if($this->game->status > 1) return ['success' => false];

        $bot = DB::table('users_fake')->where('add_bet', 0)->inRandomOrder()->first();

        if(is_null($bot)) return ['success' => false];

        $types = ['blue', 'blue', 'blue', 'blue', 'green', 'green', 'green', 'yellow', 'yellow', 'red'];
        shuffle($types);

        $value = floor(mt_rand($this->config->double_min_bet, $this->config->double_min_bet+mt_rand(0, 500))/10)*10;
        if($value < $this->config->double_min_bet) $value = 100;

        if(iconv_strlen($bot->username) > 8) $bot->username = substr($bot->username, 0, 8).'...';

        $bet = [
            'game_id' => $this->game->id,
            'user_id' => $bot->id,
            'username' => $bot->username,
            'avatar' => $bot->avatar,
            'type'    => $types[mt_rand(0, count($types)-1)],
            'value'   => $value,
            'is_fake' => 1
        ];

        DB::table('double_bets')->insert($bet);

        $username = $bot->username2;
        // if(iconv_strlen($username) > 8) $username = iconv_substr($username, 0, 8).'...';

        $bet = [
            'bet' => [
                'username' => $username,
                'value' => $bet['value'],
                'type' => $bet['type']
            ],
            'top' => $this->getTopBets()
        ];

        if($bet['bet']['value'] >= 1000) $bet['bet']['value'] = round($bet['bet']['value']/1000, 1).'K';

        $this->redis->publish('double.new.bet', json_encode($bet));

        $this->checkTimer();

        $lvl = $bot->lvl;
        $xp = $bot->xp + $value;
        while($xp >= $bot->need_xp) {
            $xp -= $bot->need_xp;
            $lvl++;
            $bot->need_xp = $bot->need_xp + floor(($bot->need_xp/100)*75);
        }

        DB::table('users_fake')->where('id', $bot->id)->update(['add_bet' => 1, 'xp' => $xp, 'lvl' => $lvl]);

        return [
            'success' => true,
            'id'      => $bot->id
        ];

    }

    public function test()
    {
        // if(count(DB::table('double_bets')->where('game_id', $this->game->id)->where('is_fake', 0)->groupBy('user_id')->get()) < 1) return false;
        //
        // $lastGame = Double::where('status', 3)->orderBy('id', 'desc')->first();
        // $set = false;
        // if($lastGame->color == 'red') $set = 'red';
        //
        // $array = ['blue', 'green', 'yellow', 'red'];
        // $all = DB::table('double_bets')->where('game_id', $this->game->id)->where('is_fake', 0)->sum('value');
        //
        // foreach($array as $key => $color) {
        //     $array[$key] = [
        //         'color' => $color,
        //         'price' => DB::table('double_bets')->where('game_id', $this->game->id)->where('type', $color)->where('is_fake', 0)->sum('value')
        //     ];
        // }
        //
        // foreach($array as $key => $item) {
        //     switch ($item['color']) {
        //         case 'blue':
        //             $array[$key]['multiplier'] = 2;
        //             break;
        //         case 'green':
        //             $array[$key]['multiplier'] = 4;
        //             break;
        //         case 'yellow':
        //             $array[$key]['multiplier'] = 10;
        //             break;
        //         case 'red':
        //             $array[$key]['multiplier'] = 20;
        //             break;
        //     }
        //
        //
        //     $array[$key]['price'] *= $array[$key]['multiplier'];
        //     $array[$key]['profit'] = $all-$array[$key]['price'];
        // }
        //
        // $list = [];
        //
        // foreach($array as $key => $item) if($item['profit'] > 0 && $item['color'] != $set) $list[] = [
        //     'color' => $item['color'],
        //     'profit' => $item['profit']
        // ];
        //
        // usort($list, function($a,$b) {
        //     return($b['profit']-$a['profit']);
        // });
        //
        // if(count($list) < 1) return false;
        //
        // return $list[mt_rand(0, count($list)-1)];
        $users = DB::table('users_fake')->get();
        foreach($users as $user) {
            if(iconv_strlen($user->username2) > 8) $user->username2 = iconv_substr($user->username2, 0, 8).'...';
            DB::table('users_fake')->where('id', $user->id)->update(['username2' => $user->username2]);
        }
        return 'success';
    }

    public function getProfit()
    {
        // if(count(DB::table('double_bets')->where('game_id', $this->game->id)->where('is_fake', 0)->groupBy('user_id')->get()) < 1) return false;

        $array = ['blue', 'green', 'yellow', 'red'];
        $all = DB::table('double_bets')->where('game_id', $this->game->id)->where('is_fake', 0)->sum('value');
        $allPercents = 0;

        foreach($array as $key => $color) {
            $array[$key] = [
                'color' => $color,
                'price' => DB::table('double_bets')->where('game_id', $this->game->id)->where('type', $color)->where('is_fake', 0)->sum('value')
            ];
        }

        foreach($array as $key => $item) {
            switch ($item['color']) {
                case 'blue':
                    $array[$key]['multiplier'] = 2;
                    $array[$key]['percent'] = $this->config->double_blue_percent;
                    break;
                case 'green':
                    $array[$key]['multiplier'] = 3;
                    $array[$key]['percent'] = $this->config->double_green_percent;
                    break;
                case 'yellow':
                    $array[$key]['multiplier'] = 5;
                    $array[$key]['percent'] = $this->config->double_yellow_percent;
                    break;
                case 'red':
                    $array[$key]['multiplier'] = 10;
                    $array[$key]['percent'] = $this->config->double_red_percent;
                    break;
            }


            $allPercents += $array[$key]['percent'];
            $array[$key]['price'] *= $array[$key]['multiplier'];
            $array[$key]['profit'] = $all-$array[$key]['price'];
        }

        $list = [];
        $count = 0;

        foreach($array as $key => $item) {
            $list[] = [
                'color' => $item['color'],
                'profit' => $item['profit'],
                'percent' => $item['percent']
            ];
            if($item['profit'] > 0) $count++;
        }

        usort($list, function($a,$b) {
            return($b['profit']-$a['profit']);
        });

        if($count > 0) {
            $p = (100-$allPercents)/$count;
            foreach($list as $key => $item) if($item['profit'] > 0) $list[$key]['percent'] += $p;
        }

        $rList = [];
        foreach($list as $item) for($i = 0; $i < ceil($item['percent']); $i++) $rList[] = $item['color'];
        shuffle($rList);

        if(count($rList) < 1) return false;

        return $rList[mt_rand(0, count($rList)-1)];
    }
}
