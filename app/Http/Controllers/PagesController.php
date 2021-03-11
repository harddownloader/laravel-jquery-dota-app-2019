<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\User;
use App\Settings;
use App\Items;
use Illuminate\Http\Request;
use Redis;
use Carbon\Carbon;
// use App\Order;
// use App\Mail\OrderShipped;
use Illuminate\Support\Facades\Mail;
use Session;


class PagesController extends Controller
{
    public function __construct()
    {
        if(Auth::check()) {
            $this->user = Auth::user();
            view()->share('u', $this->user);
        }
        $this->config = Settings::first();
        view()->share('config', $this->config);

        $this->lang = Parent::getLang();
        view()->share('lang', $this->lang);
    }

    public function profile()
    {
        parent::setTitle($this->user->username);

        $achievements = json_decode($this->user->achievements);

        return view('pages.profile', compact('achievements'));
    }

    public function index()
    {
        parent::setTitle($this->config->sitename);
        return view('pages.index');
    }

    public function support(Request $r)
    {
        mail($this->config->site_email, $r->get('subject'), $r->get('message').' Email : '.$r->get('email'));

        Mail::send('mail.mail', ['msg' => $r->get('message'), 'email' => $r->get('email'), 'sub' => $r->get('subject')], function($message) {
            $message->to('support@dotaregal.com')->subject($this->user->username.' обратился в техническую поддержку!');
            // $message->to('p4r4p3t@mail.ru')->subject($this->user->username.' обратился в техническую поддержку!');
        });

        return [
            'success' => true,
            'msg' => 'Ваше сообщение будет рассмотрено в ближайшее время'
        ];
    }

    public function getMyBalance()
    {
        if(Auth::guest()) return ['balance' => 0];
        return ['balance' => $this->user->money];
    }

    public function test5($steamid64)
    {
        $user = User::where('steamid64', $steamid64)->first();
        if(is_null($user)) return false;
        $win = 0;
        $sum = 0;
        $bets = DB::table('double_bets')->where('user_id', $user->id)->get();
        foreach($bets as $bet) {
            if($bet->is_win) {
                $price = floatval($bet->value);
                switch ($bet->type) {
                    case 'blue':
                        $price *= 2;
                        break;
                    case 'green':
                        $price *= 4;
                        break;
                    case 'yellow':
                        $price *= 10;
                        break;
                    case 'red':
                        $price *= 20;
                        break;
                }
                $win += $price;
            }
            $sum += $bet->value;
        }

        if($sum < $win) return false;
        return true;
    }

    public function test2($steamid64)
    {
        // $deposits = DB::table('deposits')->where('user_id', $steamid64)->where('status', 1)->sum('price');
        // $withdraws = DB::table('withdraws')->where('user_id', $steamid64)->where('status', 1)->sum('price');
        // echo $deposits.' / '.$withdraws;

        $user = User::where('steamid64', $steamid64)->first();
        $array = [
            'blue' => 0,
            'green' => 0,
            'yellow' => 0,
            'red' => 0
        ];

        if(!is_null($user)) {
            $bets = DB::table('double_bets')
                ->select(
                    DB::raw('SUM(value) as value'),
                    'game_id',
                    'type',
                    'created_at'
                    )
                ->where('user_id', $user->id)
                ->where('is_fake', 0)
                ->groupBy('game_id')
                ->get();
            $price = 0;
            echo '<table>';
            echo '<thead>';
            echo '<th>ID игры</th>';
            echo '<th>Сумма ставки</th>';
            echo '<th>Множитель</th>';
            echo '<th>Выигрыш</th>';
            echo '<th>Дата</th>';
            // echo '<th>Антиминус</th>';
            echo '<th>Профит</th>';
            echo '</thead>';
            echo '<tbody>';
            foreach($bets as $bet) {
                // $count = count(DB::table('double_bets')->where('game_id', $bet->game_id)->where('is_fake', 0)->groupBy('user_id')->get());
                $game = DB::table('double')->where('id', $bet->game_id)->first();
                $array[$bet->type]++;
                $price += ($bet->value*$this->getMultiplier($bet->type));
                echo '<tr>';
                echo '<td>'.$bet->game_id.'</td>';
                echo '<td>'.$bet->value.'</td>';
                echo '<td>'.$bet->type.'</td>';
                echo '<td>'.($bet->value*$this->getMultiplier($bet->type)).'</td>';
                echo '<td>'.$bet->created_at.'</td>';
                // if($count >= 3) echo '<td>Работал</td>'; else  echo '<td>Не работал</td>';
                echo '<td>'.$game->profit.'</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';

            echo '<br>';
            echo $price;
            echo '<br>';
            echo json_encode($array);
            // foreach($bets as $bet) {
            //     switch ($bet->type) {
            //         case 'blue':
            //             $bet->value *= 2;
            //             break;
            //         case 'green':
            //             $bet->value *= 4;
            //             break;
            //         case 'yellow':
            //             $bet->value *= 10;
            //             break;
            //         case 'red':
            //             $bet->value *= 20;
            //             break;
            //     }
            //     $price += $bet->value;
            //     $array[$bet->type]++;
            // }
            // echo '<br>'.$price;
            // echo '<br>';
            // echo '<br>';
            // echo '<br>';
            // echo '<br>';
            // echo json_encode($array);
        }
    }

    private function getMultiplier($color)
    {
        switch ($color) {
            case 'blue':
                return 2;
                break;
            case 'green':
                return 4;
                break;
            case 'yellow':
                return 10;
                break;
            case 'red':
                return 20;
                break;
        }
        return 0;
    }

    public function test512312($userID)
    {
        $bets = DB::table('double_bets')->where('user_id', $userID)->where('is_fake', 0)->get();
        $values = 0;
        $summ = 0;
        foreach($bets as $bet) {
            $game = DB::table('double')->where('id', $bet->game_id)->first();
            if(!is_null($game)) {
                $values += $bet->value;
                $value = $bet->value;
                if($game->color == $bet->type) $value *= $game->multiplier; else $value = 0;
                $summ += $value;
                echo $bet->value.' = '.$bet->type.' (x'.$game->multiplier.') - '.$value.' (#'.$game->id.')<br>';
            }
        }

        echo $values.'/'.$summ.' - PROFIT = '.($summ-$values).'<br>';
        // return $games;
    }

    public function test()
    {
        Mail::send('mail.mail', ['msg' => 'message', 'email' => 'email'], function($message) {
            $message->to('support@dotaregal.com')->subject($this->user->username.' обратился в техническую поддержку!');
            // $message->to('p4r4p3t@mail.ru')->subject($this->user->username.' обратился в техническую поддержку!');
        });
        // return json_encode($res);
    }

    public function test131231()
    {
        // .return strtotime(Date('Y-m-d H:i:s'))-(3600*48);
        $users = User::get();
        foreach($users as $user) echo json_encode($user->username).' - '.$user->username.'<br>';
    }

    public function tutorial()
    {
        parent::setTitle('Tutorial');
        return view('pages/tutorial');
    }
}
