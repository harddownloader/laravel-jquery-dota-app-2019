<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use App\Bots;
use App\Settings;
use App\Shop;
use App\Promo;
use Auth;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Redis;

class AdminController extends Controller
{
    public function __construct()
    {
        if(Auth::check()) {
            $this->user = Auth::user();
            view()->share('u', $this->user);
        }
        $this->config = Settings::first();
        view()->share('config', $this->config);
        $this->redis = Redis::connection();

        $this->lang = parent::getLang();
    }

    public function itemsList()
    {
        $list = [
            'withdraws' => DB::table('withdraws')->get(),
            'deposits' => DB::table('deposits')->get()
        ];

        foreach($list['withdraws'] as $key => $w)
        {
            $list['withdraws'][$key]->items = json_decode($w->items);
            $list['withdraws'][$key]->user = User::where('id', $w->user_id)->first();
        }

        foreach($list['deposits'] as $key => $d)
        {
            $list['deposits'][$key]->items = json_decode($d->items);
            $list['deposits'][$key]->user = User::where('id', $d->user_id)->first();
        }

        parent::setTitle('Предметы');

        return view('admin.items', compact('list'));
    }

    public function index()
    {
        Controller::setTitle('Статистика');

        $stats = $this->getStats();

        return view('admin.index', compact('stats'));
    }

    private function getStats()
    {
        $dd = DB::table('deposits')->where('status', 3)->where('created_at', '>', Carbon::now()->startOfDay())->sum('value');
        $dw = DB::table('deposits')->where('status', 3)->where('created_at', '>', Carbon::now()->startOfWeek())->sum('value');
        $dm = DB::table('deposits')->where('status', 3)->where('created_at', '>', Carbon::now()->startOfMonth())->sum('value');
        $da = DB::table('deposits')->where('status', 3)->sum('value');

        $wd = DB::table('withdraws')->where('status', 4)->where('created_at', '>', Carbon::now()->startOfDay())->sum('value');
        $ww = DB::table('withdraws')->where('status', 4)->where('created_at', '>', Carbon::now()->startOfWeek())->sum('value');
        $wm = DB::table('withdraws')->where('status', 4)->where('created_at', '>', Carbon::now()->startOfMonth())->sum('value');
        $wa = DB::table('withdraws')->where('status', 4)->sum('value');

        $data = [
            'day' => round($dd-$wd, 2),
            'week' => round($dw-$ww, 2),
            'month' => round($dm-$wm, 2),
            'all' => round($da-$wa, 2)
        ];

        return $data;
    }

    public function bots()
    {
        $bots = Bots::get();
        Controller::setTitle('Боты');
        return view('admin.bots', compact('bots'));
    }

    public function bot_edit($id)
    {
        $bot = Bots::where('id', $id)->first();
        if(is_null($bot)) return view('errors.404');
        Controller::setTitle('Редактирование бота #'.$id);
        return view('admin.bot_edit', compact('bot'));
    }

    public function users()
    {
        Controller::setTitle('Пользователи');
        $users = [];
        return view('admin.users', compact('users'));
    }

    public function usersGet(Request $r)
    {
        $list = User::orderBy($r->get('col'), $r->get('sort'))->limit(100)->get();
        return $list;
    }

    public function user_edit($id)
    {
        $user = User::where('id', $id)->first();
        if(is_null($user)) return view('errors.404');
        Controller::setTitle('Редактирование пользователя '.$user->username);
        return view('admin.user_edit', compact('user'));
    }

    public function users_fake()
    {
        $users = DB::table('users_fake')->get();
        Controller::setTitle('ИИ Пользователи');
        return view('admin.users_fake', compact('users'));
    }

    public function settings()
    {
        $config = $this->config;
        Controller::setTitle('Настройки сайта');
        return view('admin.settings', compact('config'));
    }

    public function shop_settings()
    {
        $config = $this->config;
        Controller::setTitle('Настройки магазина');
        $presets = DB::table('presets')->where('type', 'shop')->orderBy('id', 'desc')->get();
        return view('admin.shop_settings', compact('config', 'presets'));
    }

    public function double()
    {
        $config = $this->config;
        if(!is_null($config->double_multiplier)) {
            $ar = json_decode($config->double_multiplier);
            $str = '';
            foreach($ar as $multiplier) $str .= $multiplier.',';
            if($str{strlen($str)-1} == ',') $str = substr($str, 0, -1);
            $config->double_multiplier = $str;
        }
        Controller::setTitle('Настройки дабла');
        //return $config;

        $presets = DB::table('presets')->where('type', 'double')->orderBy('id', 'desc')->get();
        return view('admin.double', compact('config', 'presets'));
    }

    public function poker()
    {
        $config = $this->config;
        Controller::setTitle('Настройки покера');
        $presets = DB::table('presets')->where('type', 'poker')->orderBy('id', 'desc')->get();
        return view('admin.poker', compact('config', 'presets'));
    }

    public function bandit()
    {
        $config = $this->config;
        $images = DB::table('bandit_images')->orderBy('id', 'desc')->get();
        foreach($images as $img) {
            $str = '';
            foreach(json_decode($img->multiplier) as $i) $str .= $i.',';
            $str = substr($str, 0, -1);
            $img->multiplier = $str;
        }
        $str = '';
        $list = json_decode($this->config->bandit_free_spins_count);
        foreach($list as $l) $str .= $l.',';
        $config->bandit_free_spins_count = substr($str, 0, -1);

        $str2 = '';
        $list2 = json_decode($this->config->bandit_countpercents);
        foreach($list2 as $l) $str2 .= $l.',';
        $config->bandit_countpercents = substr($str2, 0, -1);

        $presets = DB::table('presets')->where('type', 'bandit')->orderBy('id', 'desc')->get();

        Controller::setTitle('Настройки однорукого бандита');
        return view('admin.bandit', compact('config', 'images', 'presets'));
    }

    // public function bandit_edit_combo($id)
    // {
    //     $combo = DB::table('bandit_combo')->where('id', $id)->first();
    //     if(is_null($combo)) return view('errors.404');
    //     Controller::setTitle('Редактирование комбинации #'.$id);
    //
    //     $str = '';
    //     foreach(json_decode($combo->combo) as $img_id) $str .= $img_id.',';
    //     $str = substr($str, 0, -1);
    //     $combo->combo = $str;
    //
    //     return view('admin.bandit_edit_combo', compact('combo'));
    // }

    // public function bandit_delete_combo($id)
    // {
    //     $combo = DB::table('bandit_combo')->where('id', $id)->first();
    //     if(is_null($combo)) return view('errors.404');
    //     DB::table('bandit_combo')->where('id', $id)->delete();
    //     return redirect('/admin/bandit');
    // }

    private function getMultipliers($array)
    {
        $str = '';
        foreach(json_decode($array) as $i) $str .= $i.',';
        $str = substr($str, 0, -1);
        return $str;
    }

    public function bandit_edit_image($id)
    {
        $image = DB::table('bandit_images')->where('id', $id)->first();
        if(is_null($image)) return view('errors.404');
        Controller::setTitle('Редактирование изображения #'.$id);

        $image->multiplier = $this->getMultipliers($image->multiplier);
        $image->multiplier2 = $this->getMultipliers($image->multiplier2);
        $image->multiplier3 = $this->getMultipliers($image->multiplier3);
        $image->multiplier4 = $this->getMultipliers($image->multiplier4);
        $image->multiplier5 = $this->getMultipliers($image->multiplier5);
        $image->multiplier6 = $this->getMultipliers($image->multiplier6);
        $image->multiplier7 = $this->getMultipliers($image->multiplier7);
        $image->multiplier8 = $this->getMultipliers($image->multiplier8);
        $image->multiplier9 = $this->getMultipliers($image->multiplier9);

        return view('admin.bandit_edit_img', compact('image'));
    }

    public function chat()
    {
        $config   = $this->config;
        $messages = json_decode($this->config->chat_double_messages);
        #return $messages;
        Controller::setTitle('Настройки чата');
        return view('admin.chat', compact('config', 'messages'));
    }

    // public function getBanditCombos()
    // {
    //     $list = DB::table('bandit_combo')->orderBy('id', 'desc')->get();
    //     foreach($list as $i => $combo) {
    //         $u = json_decode($combo->combo);
    //         $l = [];
    //         foreach($u as $img) {
    //             $url = DB::table('bandit_images')->where('id', $img)->first();
    //             if(!is_null($url)) $l[] = $url->url;
    //         }
    //         $list[$i]->combo = $l;
    //     }
    //     return $list;
    // }

    /*
     * POST
     */

    public function save_user(Request $r)
    {
        $user = User::where('id', $r->get('id'))->first();
        if(is_null($user)) return [
            'success' => false,
            'msg'     => 'Не удалось найти пользователя #'.$r->get('id').' в базе данных!'
        ];

        $user->username     = $r->get('username');
        $user->avatar       = $r->get('avatar');
        $user->trade        = $r->get('trade');
        $user->money        = $r->get('money');
        $user->flagState    = $r->get('flagState');
        $user->is_banned    = $r->get('is_banned');
        $user->permission   = $r->get('permission');
        $user->lvl          = $r->get('lvl');
        $user->xp           = $r->get('xp');
        $user->n_xp = $r->get('n_xp');
        $user->roulette = $r->get('roulette');
        $user->poker = $r->get('poker');
        $user->slot_machine = $r->get('slot_machine');
        $user->ref = $r->get('ref');
        $user->my_ref = $r->get('my_ref');
        $user->save();

        AchievementController::checkAchievement($user, $this->redis, $this->lang['achievement_unlock']);


        return [
            'success' => true,
            'msg'     => 'Данные пользователя #'.$r->get('id').' успешно обновлены!'
        ];
    }

    public function banUser(Request $r)
    {
        $user = User::where('id', $r->get('id'))->first();
        if(is_null($user)) return [
            'success' => false,
            'msg'     => 'Не удалось найти пользователя #'.$r->get('id').' в базе данных!'
        ];

        $user->is_banned = $r->get('arg');
        $user->save();

        switch($r->get('arg')) {
            case 0 :
                return [
                    'success'   => true,
                    'msg'       => 'Пользователь #'.$r->get('id').' успешно разблокирован!'
                ];
            break;
            case 1 :
                return [
                    'success'   => true,
                    'msg'       => 'Пользователь #'.$r->get('id').' успешно заблокирован!'
                ];
            break;
        }

        return ['success' => false];
    }

    public function setBotOnline(Request $r)
    {
        // $bot = Bots::where('id', $r->get('id'))->first();
        // if(is_null($bot)) return [
        //     'success'   => false,
        //     'msg'       => 'Не удалось найти бота #'.$r->get('id').' в базе данных!'
        // ];
        //
        // if($bot->online == $r->get('online')) {
        //     if($r->get('online') == 0) return [
        //         'success'   => false,
        //         'msg'       => 'Бот уже выключен!'
        //     ];
        //     if($r->get('online') == 1) return [
        //         'success'   => false,
        //         'msg'       => 'Бот уже включен!'
        //     ];
        // }
        //
        // // Включение/Выключение бота
        // $bot->online = $r->get('online');
        // $bot->save();
        //
        // switch($r->get('online')) {
        //     case 0 :
        //         return [
        //             'success'   => true,
        //             'msg'       => 'Бот #'.$r->get('id').' успешно выключен!',
        //             'state'     => $r->get('online')
        //         ];
        //     break;
        //     case 1 :
        //         return [
        //             'success'   => true,
        //             'msg'       => 'Бот #'.$r->get('id').' успешно включен!',
        //             'state'     => $r->get('online')
        //         ];
        //     break;
        // }
        //
        // return ['success' => false];

        putenv("HOME=/var/www/html/pill/storage/app/");

        $start = null;
        $start2 = null;
        $stop = null;
        $stop2 = null;

        switch ($r->get('bot')) {
            case 1:
                $start = new Process('pm2 start /var/www/html/fillBot/bot.js');
                $stop = new Process('pm2 stop bot');
                $start2 = new Process('pm2 start /var/www/html/fillBot/owner.js');
                $stop2 = new Process('pm2 stop owner');
                break;
            case 0 :
                $start = new Process('pm2 start /var/www/html/fillBot/app.js');
                $stop = new Process('pm2 stop app');
                break;
        }

        if($r->get('type') == 0) {
            $stop->start();
            if($r->get('bot') == 1) $stop2->start();
        } elseif($r->get('type') == 1) {
            $start->start();
            if($r->get('bot') == 1) $start2->start();
        }

        return [
            'success' => true,
            'msg' => 'Операция успешно выполнена!'
        ];

    }

    public function save_bot(Request $r)
    {
        $bot = Bots::where('id', $r->get('id'))->first();
        if(is_null($bot)) return [
            'success'   => false,
            'msg'       => 'Не удалось найти бота #'.$r->get('id').' в базе данных!'
        ];

        $bot->username          = $r->get('username');
        $bot->password          = $r->get('password');
        $bot->shared_secret     = $r->get('shared_secret');
        $bot->identity_secret   = $r->get('identity_secret');
        $bot->trade             = $r->get('trade');
        $bot->save();

        return [
            'success'   => true,
            'msg'       => 'Бот #'.$r->get('id').' сохранен успешно!'
        ];
    }

    public function settings_save(Request $r)
    {
        $this->config->sitename     = $r->get('sitename');
        $this->config->descriptions = $r->get('descriptions');
        $this->config->keywords     = $r->get('keywords');
        $this->config->site_email     = $r->get('site_email');
        $this->config->alert_active     = $r->get('alert_active');
        $this->config->alert_message_ru     = $r->get('alert_message_ru');
        $this->config->alert_message_en     = $r->get('alert_message_en');
        $this->config->alert_type     = $r->get('alert_type');
        $this->config->facebook = $r->get('facebook');
        $this->config->vk = $r->get('vk');
        $this->config->youtube = $r->get('youtube');
        $this->config->twitter = $r->get('twitter');
        $this->config->ref_own_money = $r->get('ref_own_money');
        $this->config->ref_rem_money = $r->get('ref_rem_money');
        $this->config->ref_percent = $r->get('ref_percent');
        $this->config->ref_count = $r->get('ref_count');
        #$this->config->ii_active    = $r->get('ii_active');
        $this->config->save();

        return [
            'success'   => true,
            'msg'       => 'Настройки сайта успешно сохранены!'
        ];
    }

    public function save_shop_settings(Request $r)
    {
        $this->config->dep_minprice_item    = $r->get('dep_minprice_item');
        $this->config->dep_minprice         = $r->get('dep_minprice');
        $this->config->with_minprice_item   = $r->get('with_minprice_item');
        $this->config->with_minprice        = $r->get('with_minprice');
        $this->config->shop_curs            = $r->get('shop_curs');
        $this->config->with_lvl             = $r->get('with_lvl');
        $this->config->with_percent     = $r->get('with_percent');
        // if($this->config->with_percent != $r->get('with_percent')) {
        //     $items = Shop::get();
        //     foreach($items as $item) {
        //         $item->price = ($item->price/(100+$this->config->with_percent))*(100 + $r->get('with_percent'));
        //         // $item->price = ($item->price/100)*$r->get('with_percent');
        //         // $item->price = round($item->price);
        //         $item->save();
        //     }
        //     $this->config->with_percent     = $r->get('with_percent');
        // }
        $this->config->with_max_items       = $r->get('with_max_items');
        $this->config->save();

        return [
            'success'   => true,
            'msg'       => 'Настройки магазина успешно сохранены!'
        ];
    }

    public function save_double(Request $r)
    {
        if($this->config->double_timer != $r->get('double_timer')) {
            $this->redis->publish('message', json_encode([
                'success' => 'warning',
                'msg' => 'Время таймера обновлено! ('.$r->get('double_timer').'сек)',
                'room' => 'double'
            ]));
        }

        $this->config->double_timer         = $r->get('double_timer');
        $this->config->double_min_bet       = $r->get('double_min_bet');
        $this->config->double_max_bet       = $r->get('double_max_bet');
        $this->config->double_comission     = $r->get('double_comission');
        $this->config->double_timetoslider  = $r->get('double_timetoslider');
        $this->config->double_timetonewgame = $r->get('double_timetonewgame');
        $this->config->double_minplayers    = $r->get('double_minplayers');
        $this->config->double_candoit    = $r->get('double_candoit');
        $this->config->double_blue_percent = $r->get('double_blue_percent');
        $this->config->double_green_percent = $r->get('double_green_percent');
        $this->config->double_yellow_percent = $r->get('double_yellow_percent');
        $this->config->double_red_percent = $r->get('double_red_percent');
        $this->config->save();

        return [
            'success'   => true,
            'msg'       => 'Настройки дабла успешно сохранены!'
        ];
    }

    public function save_bandit(Request $r)
    {
        $this->config->bandit_min_bet   = $r->get('bandit_min_bet');
        $this->config->bandit_max_bet   = $r->get('bandit_max_bet');
        $this->config->bandit_free_spins_chance = $r->get('bandit_free_spins_chance');
        $this->config->bandit_winpercent = $r->get('bandit_winpercent');
        $this->config->bandit_winpercent2 = $r->get('bandit_winpercent2');
        $this->config->bandit_winpercent3 = $r->get('bandit_winpercent3');
        $this->config->bandit_winpercent4 = $r->get('bandit_winpercent4');
        $this->config->bandit_winpercent5 = $r->get('bandit_winpercent5');
        $this->config->bandit_winpercent6 = $r->get('bandit_winpercent6');
        $this->config->bandit_winpercent7 = $r->get('bandit_winpercent7');
        $this->config->bandit_winpercent8 = $r->get('bandit_winpercent8');
        $this->config->bandit_winpercent9 = $r->get('bandit_winpercent9');
        $this->config->bandit_quest = $r->get('bandit_quest');
        $this->config->bandit_mgame = $r->get('bandit_mgame');
        $this->config->bandit_towers = $r->get('bandit_towers');
        $this->config->bandit_bonus = $r->get('bandit_bonus');
        $this->config->bandit_free_spins_count = json_encode(explode(',', $r->get('bandit_free_spins_count')));
        $this->config->bandit_countpercents = json_encode(explode(',', $r->get('bandit_countpercents')));
        $this->config->save();

        return [
            'success'   => true,
            'msg'       => 'Настройки однорукого бандита успешно сохранены!'
        ];
    }

    public function bandit_add_img(Request $r)
    {
        if(!strpos($r->get('url'), '://')) return [
            'success' => false,
            'msg'     => 'Не удалось найти путь к изображению!'
        ];

        DB::table('bandit_images')->insert([
            'url' => $r->get('url'),
            'multiplier' => json_encode(explode(',', $r->get('multiplier'))),
            'type' => $r->get('type')
        ]);

        return [
            'success' => true,
            'msg'     => 'Изображение успешно добавлено!'
        ];
    }

    public function bandit_save_image(Request $r)
    {
        if(!strpos($r->get('url'), '://')) return [
            'success' => false,
            'msg'     => 'Не удалось найти путь к изображению!'
        ];

        DB::table('bandit_images')->where('id', $r->get('id'))->update([
            'url' => $r->get('url'),
            'multiplier' => json_encode(explode(',', $r->get('multiplier'))),
            'multiplier2' => json_encode(explode(',', $r->get('multiplier2'))),
            'multiplier3' => json_encode(explode(',', $r->get('multiplier3'))),
            'multiplier4' => json_encode(explode(',', $r->get('multiplier4'))),
            'multiplier5' => json_encode(explode(',', $r->get('multiplier5'))),
            'multiplier6' => json_encode(explode(',', $r->get('multiplier6'))),
            'multiplier7' => json_encode(explode(',', $r->get('multiplier7'))),
            'multiplier8' => json_encode(explode(',', $r->get('multiplier8'))),
            'multiplier9' => json_encode(explode(',', $r->get('multiplier9'))),
            'type' => $r->get('type')
        ]);

        return [
            'success' => true,
            'msg'     => 'Изображение успешно сохранено!'
        ];
    }

    // public function bandit_add_combo(Request $r)
    // {
    //     $combo = explode(',', $r->get('combo'));
    //     if(count($combo) != 5) return [
    //         'success' => false,
    //         'msg'     => 'Вы должны ввести 5 цифр, обозночающих id 5-ти изображений'
    //     ];
    //
    //     DB::table('bandit_combo')->insert([
    //         'combo'      => json_encode($combo),
    //         'multiplier' => $r->get('multiplier')
    //     ]);
    //
    //     return [
    //         'success' => true,
    //         'msg'     => 'Комбинация успешно добавлена!'
    //     ];
    // }

    // public function save_bandit_combo(Request $r)
    // {
    //     $combo = explode(',', $r->get('combo'));
    //     if(count($combo) != 5) return [
    //         'success' => false,
    //         'msg'     => 'Вы должны ввести 5 цифр, обозночающих id 5-ти изображений'
    //     ];
    //
    //     DB::table('bandit_combo')->where('id', $r->get('id'))->update([
    //         'combo'      => json_encode($combo),
    //         'multiplier' => $r->get('multiplier')
    //     ]);
    //
    //     return [
    //         'success' => true,
    //         'msg'     => 'Комбинация успешно сохранена!'
    //     ];
    // }

    public function save_poker(Request $r)
    {
        $this->config->poker_bet_timer   = $r->get('poker_bet_timer');
        $this->config->poker_raise_timer   = $r->get('poker_raise_timer');
        $this->config->poker_puttype = $r->get('poker_puttype');
        // $this->config->poker_min_ante   = $r->get('poker_min_ante');
        // $this->config->poker_max_ante   = $r->get('poker_max_ante');
        // $this->config->poker_min_bet    = $r->get('poker_min_bet');
        // $this->config->poker_max_bet    = $r->get('poker_max_bet');
        // $this->config->poker_comission  = $r->get('poker_comission');
        $this->config->save();

        return [
            'success'   => true,
            'msg'       => 'Настройки покера успешно сохранены!'
        ];
    }

    public function save_chat(Request $r)
    {
        // $this->config->chat_ii_poker  = $r->get('chat_ii_poker');
        // $this->config->chat_ii_double = $r->get('chat_ii_double');
        // $this->config->chat_ii_bandit = $r->get('chat_ii_bandit');
        $this->config->chat_max_strlen = $r->get('chat_max_strlen');
        $this->config->chat_min_strlen = $r->get('chat_min_strlen');
        if($r->get('chat_min_strlen') < 1) $this->config->chat_min_strlen = 1;

        // if($r->get('chat_ii_double') == 1) {
            $messages = $r->get('chat_double_messages');
            $messages = explode(';', $messages);
            $list = [];
            foreach($messages as $message) {
                $message = explode(':', $message);
                if(isset($message[0]) && isset($message[1])) {
                    $list[] = [
                        'msg'       => trim($message[0]),
                        'won'       => trim($message[1])
                    ];
                }
            }

            $this->config->chat_double_messages = json_encode($list);
        // }

        $this->config->save();

        return [
            'success' => true,
            'msg'     => 'Настройки чата успешно сохранены!'
        ];
    }

    public function proxy()
    {
        $proxy = DB::table('proxy')->get();
        parent::setTitle('Прокси');
        return view('admin.proxy', compact('proxy'));
    }

    public function edit_proxy($id)
    {
        $proxy = DB::table('proxy')->where('id', $id)->first();
        if(is_null($proxy)) return redirect()->back();
        parent::setTitle('Редактирование прокси #'.$id);
        return view('admin.edit_proxy', compact('proxy'));
    }

    public function save_proxy(Request $r)
    {
        $proxy = DB::table('proxy')->where('id', $r->get('id'))->first();

        if(is_null($proxy)) return [
            'success' => false,
            'msg' => 'Не удалось найти прокси #'.$r->get('id')
        ];

        DB::table('proxy')->where('id', $r->get('id'))->update([
            'ip' => $r->get('ip'),
            'port' => $r->get('port'),
            'login' => $r->get('login'),
            'password' => $r->get('password')
        ]);

        return [
            'success' => true,
            'msg' => 'Прокси #'.$r->get('id').' успешно изменен!'
        ];
    }

    # Diagrams
    public function getDiagrams(Request $r)
    {

        #return Carbon::parse()->addHours(23)->addMinutes(59);

        $list = [];

        if($r->get('period') == 'year') $list = DB::table('online')->where('time', '>=', Carbon::now()->startOfYear())->select('time')->groupBy('time')->get();
        elseif($r->get('period') == 'month') $list = DB::table('online')->where('time', '>=', Carbon::now()->startOfMonth())->select('time')->groupBy('time')->get();
        elseif($r->get('period') == 'week') $list = DB::table('online')->where('time', '>=', Carbon::now()->startOfWeek())->select('time')->groupBy('time')->get();

        $x = [];
        foreach($list as $l) $x[] = $l->time;

        $y = [];
        foreach($x as $date) $y[] = DB::table('online')->where('time', $date)->count();

        $y2 = [];
        foreach($x as $date) $y2[] = DB::table('users')->where('created_at', '>', $date)->where('created_at', '<', Carbon::parse($date)->addHours(23)->addMinutes(59)->addSeconds(59))->count();

        $y3 = [];
        foreach($x as $date) $y3[] = DB::table('users')->where('created_at', '>', $date)->where('created_at', '<', Carbon::parse($date)->addHours(23)->addMinutes(59)->addSeconds(59))->where('trade', '!=', null)->count();


        $w = [];
        foreach($x as $date) $w[] = count(DB::table('withdraws')->where('created_at', '>', $date)->where('created_at', '<', Carbon::parse($date)->addHours(23)->addMinutes(59)->addSeconds(59))->get());

        $wc = [];
        foreach($x as $date) $wc[] = count(DB::table('withdraws')->where('status', 4)->where('created_at', '>', $date)->where('created_at', '<', Carbon::parse($date)->addHours(23)->addMinutes(59)->addSeconds(59))->get());

        $wd = [];
        foreach($x as $date) $wd[] = count(DB::table('withdraws')->where('status', 5)->where('created_at', '>', $date)->where('created_at', '<', Carbon::parse($date)->addHours(23)->addMinutes(59)->addSeconds(59))->get());

        $wo = [];
        foreach($x as $date) $wo[] = count(DB::table('withdraws')->where('status', '<', 4)->where('created_at', '>', $date)->where('created_at', '<', Carbon::parse($date)->addHours(23)->addMinutes(59)->addSeconds(59))->get());

        $d = [];
        foreach($x as $date) $d[] = DB::table('deposits')->where('status', 3)->where('created_at', '>', $date)->where('created_at', '<', Carbon::parse($date)->addHours(23)->addMinutes(59)->addSeconds(59))->count();


        foreach($x as $key => $i) {
            $date = explode(' ', $i);
            $x[$key] = $date[0];
        }

        if($r->get('period') == 'day')
        {
            $list = [];
            for($i = 0; $i < 24; $i++)
            {
                $list[] = Carbon::today()->addHours($i)->format('Y-m-d H:00:00');
            }

            // return $list;

            $x = [];
            foreach($list as $l) $x[] = $l;

            $y = [];
            foreach($x as $date) $y[] = DB::table('online')->where('time', $date)->count();

            $y2 = [];
            foreach($x as $date) $y2[] = DB::table('users')->where('created_at', '>', $date)->where('created_at', '<', Carbon::parse($date)->addHours(1))->count();

            $y3 = [];
            foreach($x as $date) $y3[] = DB::table('users')->where('created_at', '>', $date)->where('created_at', '<', Carbon::parse($date)->addHours(1))->where('trade', '!=', null)->count();


            $w = [];
            foreach($x as $date) $w[] = count(DB::table('withdraws')->where('created_at', '>', $date)->where('created_at', '<', Carbon::parse($date)->addHours(1))->groupBy('with_id')->get());

            $wc = [];
            foreach($x as $date) $wc[] = count(DB::table('withdraws')->where('status', 4)->where('created_at', '>', $date)->where('created_at', '<', Carbon::parse($date)->addHours(1))->get());

            $wd = [];
            foreach($x as $date) $wd[] = count(DB::table('withdraws')->where('status', 5)->where('created_at', '>', $date)->where('created_at', '<', Carbon::parse($date)->addHours(1))->get());

            $wd = [];
            foreach($x as $date) $wd[] = count(DB::table('withdraws')->where('status', '<', 4)->where('created_at', '>', $date)->where('created_at', '<', Carbon::parse($date)->addHours(1))->get());

            $d = [];
            foreach($x as $date) $d[] = DB::table('deposits')->where('status', 1)->where('created_at', '>', $date)->where('created_at', '<', Carbon::parse($date)->addHours(1))->count();


            foreach($x as $key => $i) {
                $date = explode(' ', $i);
                $x[$key] = $date[1];
            }

        }


        return [
            'x' => $x,
            'o' => $y,
            'u' => $y2,
            'w' => $w,
            'wc' => $wc,
            'wd' => $wd,
            'wo' => $wo,
            'd' => $d,
            't' => $y3
        ];
    }

    public function add_preset(Request $r)
    {   
        $preset = DB::table('presets')->where('name', $r->get('name'))->first();
        if(!is_null($preset)) return [
            'success' => false,
            'msg' => 'Такое название пресета уже занято!'
        ];


        $list = $r->get('argv');
        if($r->get('type') == 'bandit') foreach($list as $key => $l) if($l['name'] == 'bandit_free_spins_count') {
            $list[$key]['value'] = '['.$l['value'].']';
        }

        DB::table('presets')->insert([
            'type' => $r->get('type'),
            'name' => $r->get('name'),
            'args' => json_encode($list)
        ]);

        return [
            'success' => true,
            'msg' => 'Пресет успешно добавлен!'
        ];
    }

    public function set_preset(Request $r)
    {
        $preset = DB::table('presets')->where('id', $r->get('id'))->first();
        if(is_null($preset)) return [
            'success' => false,
            'msg' => 'Не удалось найти пресет, который имеет id - '.$r->get('id')
        ];

        $preset->args = json_decode($preset->args);
        $list = [];
        foreach($preset->args as $l) $list[$l->name] = $l->value; 

        DB::table('settings')->update($list);

        return [
            'success' => true,
            'msg' => 'Пресет "'.$preset->name.'" успешно применен!'
        ];
    }

    public function del_preset($id)
    {
        $preset = DB::table('presets')->where('id', $id)->first();
        if(is_null($preset)) return [
            'success' => false,
            'msg' => 'Не удалось найти пресет, который имеет id - '.$id
        ];

        DB::table('presets')->where('id', $id)->delete();

        return redirect()->back();
    }

    public function edit_preset($id)
    {
        $preset = DB::table('presets')->where('id', $id)->first();
        if(is_null($preset)) return view('errors.404');

        $preset->args = json_decode($preset->args);

        parent::setTitle($preset->name);

        if($preset->type == 'bandit') {
            foreach($preset->args as $key => $l) {
                if($l->name == 'bandit_free_spins_count' || $l->name == 'bandit_countpercents') {
                    $str = '';
                    $list = json_decode($l->value);
                    foreach($list as $q) $str .= $q.',';
                    $str = substr($str, 0, -1);
                    $preset->args[$key]->value = $str;
                }
            }
            // $str = '';
            // $list = json_decode($this->config->bandit_free_spins_count);
            // foreach($list as $l) $str .= $l.',';
            // $config->bandit_free_spins_count = substr($str, 0, -1);

            // $str2 = '';
            // $list2 = json_decode($this->config->bandit_countpercents);
            // foreach($list2 as $l) $str2 .= $l.',';
            // $config->bandit_countpercents = substr($str2, 0, -1);
            return view('admin.bandit_preset', compact('preset', 'config'));
        } else if($preset->type == 'double') {
            return view('admin.double_preset', compact('preset'));
        } else if($preset->type == 'poker') {
            return view('admin.poker_preset', compact('preset'));
        } else if($preset->type == 'shop') {
            return view('admin.shop_preset', compact('preset'));
        }

        return view('errors.404');
    }

    public function save_preset(Request $r)
    {
        $preset = DB::table('presets')->where('id', $r->get('id'))->first();
        if(is_null($preset)) return [
            'success' => false,
            'msg' => 'Не удалось найти пресет, который имеет id - '.$r->get('id')
        ];

        $args = [];
        foreach($r->get('argv') as $l) if($l['name'] != 'name' && $l['name'] != 'id') $args[] = [
            'name' => $l['name'],
            'value' => $l['value']
        ];

        foreach($args as $key => $l) {
            if($l['name'] == 'bandit_free_spins_count' || $l['name'] == 'bandit_countpercents') {
                $args[$key]['value'] = json_encode(explode(',', $l['value']));
            }
        }

        $name = $preset->name;
        foreach($r->get('argv') as $l) if($l['name'] == 'name') $name = $l['value'];

        DB::table('presets')->where('id', $r->get('id'))->update([
            'name' => $name,
            'args' => json_encode($args)
        ]);

        return [
            'success' => true,
            'msg' => 'Пресет успешно добавлен!'
        ];
    }

    public function updateDarkTheme($type)
    {
        DB::table('settings')->update([
            'is_dark_theme' => $type
        ]);

        return [
            'success' => true
        ];
    }

    public function test(Request $r)
    {
        $res1 = User::where('username', 'LIKE', '%'.$r->get('result').'%')->limit(100)->get();
        $res2 = User::where('money', 'LIKE', '%'.$r->get('result').'%')->limit(100)->get();
        $res3 = User::where('permission', 'LIKE', '%'.$r->get('result').'%')->limit(100)->get();
        $res4 = User::where('ref', 'LIKE', '%'.$r->get('result').'%')->limit(100)->get();

        $result = [];
        foreach($res1 as $u) $result[] = $u;
        foreach($res2 as $u) $result[] = $u;
        foreach($res3 as $u) $result[] = $u;
        foreach($res4 as $u) $result[] = $u;

        usort($result, function($a, $b) {
            return($b->id-$a->id);
        });

        $res = [];

        foreach($result as $u)
        {
            $found = false;
            foreach($res as $user) if($u->id == $user->id) $found = true;
            if(!$found) $res[] = $u;
        }

        return $res;
    }
}

