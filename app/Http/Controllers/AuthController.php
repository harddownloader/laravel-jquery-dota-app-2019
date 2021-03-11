<?php

namespace App\Http\Controllers;

use Invisnik\LaravelSteamAuth\SteamAuth;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class AuthController extends Controller
{
    /**
     * @var SteamAuth
     */
    private $steam;

    public function __construct(SteamAuth $steam)
    {
        $this->steam = $steam;
        if(Auth::check()) $this->user = Auth::user();

        $this->lang = Parent::getLang();
    }

    public function login()
    {
        if(Auth::check()) return redirect('/');
        if ($this->steam->validate()) {
            $info = $this->steam->getUserInfo();
            if (!is_null($info)) {
                $user = User::where('steamid64', $info->steamID64)->first();
                $flagState = null;
                if(isset($info->loccountrycode)) $flagState = $info->loccountrycode;
                if (is_null($user)) {
                    $str = $this->getRef();
                    $user = User::create([
                        'username'      => $info->personaname,
                        'avatar'        => $info->avatarfull,
                        'steamid64'     => $info->steamID64,
                        'flagState'     => $flagState,
                        'ref'           => $str,
                        'achievements'  => AchievementController::insert()
                    ]);
                    AchievementController::insert($info->steamID64);
                } else {
                    $user->username = $info->personaname;
                    $user->avatar   = $info->avatarfull;
                    $user->flagState = $flagState;
                    $user->save();
                }
                Auth::login($user, true);
                return redirect('/'); // redirect to site
            }
        }
        return $this->steam->redirect(); // redirect to Steam login page
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

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function addIp(Request $r)
    {
        $ip = DB::table('online')->where('ip', $r->get('ip'))->where('time', Carbon::today())->first();
        if(!is_null($ip)) return ['success' => false];

        DB::table('online')->insert([
            'ip' => $r->get('ip'),
            'time' => Carbon::today()
        ]);

        return ['success' => true];
    }

    public function updateSettings(Request $request)
    {
        if(Auth::guest()) return;
        $user = $this->user;
        if(!$request->ajax()){
            $steamInfo = $this->_getSteamInfo($user->steamid64);
            $user->username = $steamInfo->getNick();
            $user->avatar = $steamInfo->getProfilePictureFull();
        }
        if($token = $this->_parseTradeLink($link = $request->get('url'))){
            $user->trade = $link;
            $user->save();
            if($request->ajax()) return response()->json(['msg' => 'Настройки сохранены!', 'success' => true]);
        }else{
            if($request->ajax()) return response()->json(['msg' => 'Неверная ссылка!', 'success' => false]);
        }
    }

    private function _parseTradeLink($tradeLink)
    {
        $query_str = parse_url($tradeLink, PHP_URL_QUERY);
        parse_str($query_str, $query_params);
        return isset($query_params['token']) ? $query_params['token'] : false;
    }
}
