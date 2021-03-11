<?php

namespace App\Http\Controllers;

use App\User;
use App\Chat;
use DB;
use App\Settings;
use App\Double;
use Redis;
use Carbon\Carbon;
use Auth;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct()
    {
        if(Auth::check()) $this->user = Auth::user();
        $this->redis  = Redis::connection();
        $this->config = Settings::first();
        $this->double = Double::orderBy('id', 'desc')->first();

        $this->lang = Parent::getLang();
    }

    public function sendMessage(Request $r)
    {
        if(Auth::guest()) return;

        if($this->user->is_banned) return [
            'success' => false,
            'msg'     => 'Вы были заблокированы на данном ресурсе!'
        ];

        if((strpos($r->get('message'), 'script>') || strpos($r->get('message'), 'link>') || strpos($r->get('message'), 'http') || strpos($r->get('message'), 'https') || strpos($r->get('message'), '://')) > 0) return [
            'success' => false,
            'msg' => 'Нельзя отправлять подобные сообщения в чат!'
        ];

        $room = str_replace('/', '', $r->get('room'));
        switch($room) {
            case 'double' : break;
            case 'poker' : break;
            case 'bandit' : break;
            case 'banditpaytable' : $room = 'bandit'; break;
            default : return [
                'success' => false,
                'msg' => 'Не удалось найти комнату, в которую вы пытаетесь отправить сообщение! '.$room
            ];
            break;
        }

        // $message = str_replace(' ', '', $r->get('message'))

        if(iconv_strlen(str_replace(' ', '', $r->get('message'))) < $this->config->chat_min_strlen) return [
            'success' => false,
            'msg'     => 'Минимальное количество символов в сообщении - '.$this->config->chat_min_strlen
        ];

        if(($this->config->chat_max_strlen != 0) && (iconv_strlen(str_replace(' ', '', $r->get('message'))) > $this->config->chat_max_strlen)) return [
            'success' => false,
            'msg'     => 'Максимальное количество символов в сообщении - '.$this->config->chat_max_strlen
        ];

        AchievementController::checkAchievement($this->user, $this->redis, $this->lang['achievement_unlock']);


        $returnValue = [
            'user_id'  => $this->user->id,
            'message'  => $r->get('message'),
            'room'     => $room,
            'is_fake'  => 0,
            'time'     => Carbon::now('MSK')->format('H:i')
        ];

        Chat::insert($returnValue);

        $messages = Chat::where('room', $room)->limit(20)->orderBy('id', 'desc')->get();
        if(count($messages) == 20) Chat::where('id', '<', $messages[19]->id)->where('room', $room)->delete();

        $returnValue['user_id'] = null;
        $returnValue['username'] = $this->user->username;
        $returnValue['avatar'] = $this->user->avatar;
        $returnValue['lvl'] = $this->user->lvl;

        $this->redis->publish('chat.new.msg', json_encode($returnValue));

        return [
            'success' => true,
            'msg'     => 'Сообщение было успешно отправлено!'
        ];

    }

    public function DoubleFakeMessages()
    {
        $list = json_decode($this->config->chat_double_messages);
        if(count($list) < 1) return ['success' => false];
        $key = mt_rand(0, count($list)-1);
        $message = $list[$key];

        if($message->won != 2) {
            $bot = DB::table('double_bets')->where('game_id', $this->double->id-1)->where('is_fake', 1)->where('is_win', $message->won)->first();
            if(is_null($bot)) return ['success' => false];
            $bot = DB::table('users_fake')->where('id', $bot->user_id)->first();
            if(is_null($bot)) return ['success' => false];
        } else {
            $bot = DB::table('users_fake')->where('post_message', 0)->inRandomOrder()->first();
            if(is_null($bot)) {
                DB::table('users_fake')->update(['post_message' => 0]);
                $bot = DB::table('users_fake')->where('post_message', 0)->inRandomOrder()->first();
            }
            if(is_null($bot)) return ['success' => false];
        }

        $returnValue = [
            'user_id'  => $bot->id,
            'message'  => $message->msg,
            'room'     => 'double',
            'is_fake'  => 1,
            'time'     => Carbon::now('MSK')->format('H:i')
        ];

        Chat::insert($returnValue);

        $returnValue['user_id'] = null;
        $returnValue['username'] = $bot->username;
        $returnValue['avatar'] = $bot->avatar;
        $returnValue['lvl'] = $this->getLvl($bot->id);


        $this->redis->publish('chat.new.msg', json_encode($returnValue));

        // Set `post_message`, 1
        DB::table('users_fake')->where('id', $bot->id)->update(['post_message' => 1]);

        $messages = Chat::where('room', 'double')->limit(20)->orderBy('id', 'desc')->get();
        if(count($messages) == 20) Chat::where('id', '<', $messages[19]->id)->where('room', 'double')->delete();

        return [
            'success' => true,
            'id'      => $bot->id,
            'message' => $message->msg,
            'room'    => 'double'
        ];
    }

    public static function getLvl($id)
    {
        $lvl = 1;
        $xp = DB::table('double_bets')->where('user_id', $id)->where('is_fake', 1)->sum('value');
        $need = 10000; // 10.000
        while($xp >= $need) {
            $xp -= $need;
            $lvl++;
            $need += floor($need/100)*75;
        }

        return $lvl;
    }

    public static function getMessages($room)
    {
        $messages = DB::table('chat')->where('room', $room)->limit(20)->orderBy('id', 'desc')->get();
        $messges = array_reverse($messages);
        $list = [];

        foreach($messages as $message) {
            if($message->is_fake) {
                $user = DB::table('users_fake')->where('id', $message->user_id)->first();
                $lvl = ChatController::getLvl($user->id);
            } else {
                $user = User::where('id', $message->user_id)->first();
                $lvl = $user->lvl;
            }

            if(!is_null($user)) $list[] = [
                'username' => $user->username,
                'avatar' => $user->avatar,
                'message' => $message->message,
                'room' => $message->room,
                'lvl' => $lvl,
                'time' => $message->time
            ];
        }

        $list = array_reverse($list);

        return $list;
    }

    public function clear($room)
    {
        Chat::where('room', $room)->delete();
    }
}
