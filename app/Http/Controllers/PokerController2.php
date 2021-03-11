<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use App\User;
use App\Poker;
use Illuminate\Http\Request;
use Redis;
use Session;

class PokerController extends Controller
{
    public function __construct()
    {
        $this->redis = Redis::connection();
        if(Auth::check()) {
            $this->user = Auth::user();
            view()->share('u', $this->user);
        }

        $this->lang = Parent::getLang();
    }
    
    public function index()
    {
        $game = $this->getGame();
        return view('pages.poker', compact('game'));
    }
    
    public function newBet(Request $r)
    {
        if(Auth::guest()) return ['success' => false];
        
        $game = Poker::where('status', 2)->where('user_id', $this->user->id)->first();
        if(!is_null($game)) {
            // x2 Ante
            if(($game->price*2) > $r->get('value')) return [
                'success' => false,
                'msg' => 'Ставка должна быть в 2 раза больше предыдущей!'
            ];
            $game->price += $r->get('value');
            $game->status = 3;
            $game->save();
            
            $user = json_decode($game->other_cards);
            $diler = json_decode($game->other_cards);
            foreach(json_decode($game->user_cards) as $card) $user[] = $card;
            foreach(json_decode($game->diler_cards) as $card) $diler[] = $card;
            
            $dc = $this->checkCombo($diler);
            $uc = $this->checkCombo($user);
            
            $winner = null;
            
            if(($dc['id'] == 1) && ($uc['id'] == 1)) {
                $dh = $this->getHighCard(json_decode($game->diler_cards));
                $uh = $this->getHighCard(json_decode($game->user_cards));
                if($dh > $uh) $winner = 'diler';
                if($uh > $dh) $winner = 'user';
            }
            
            if($dc['id'] > $uc['id']) $winner = 'diler';
            if($uc['id'] > $dc['id']) $winner = 'user';
            if(($dc['id'] == $uc['id']) && ($dc['id'] != 1) && ($uc['id'] != 1)) {
                // Одинаковые комбинации
                if($dc['sum'] > $uc['sum']) $winner = 'diler';
                if($uc['sum'] > $dc['sum']) $winner = 'user';
                if($dc['sum'] == $uc['sum']) $winner = 'nope';
            }
            
            $game->winner = $winner;
            
            $game->dc = $dc['name'];
            $game->uc = $uc['name'];
            $game->save();
            
            $game->user_cards = json_decode($game->user_cards);
            $game->diler_cards = json_decode($game->diler_cards);
            $game->other_cards = json_decode($game->other_cards);
            
            if($winner == 'user') $this->user->money += $game->price*2;
            
            return ['success' => true, 'msg' => 'Вы внесли анте х2', 'game' => $game];
        }
        
        $game = $this->newGame($r->get('value'));
        
        return [
            'success' => true,
            'msg' => 'Вы успешно создали новую игру!',
            'game' => $game
        ];
        
    }
    
    public function getHighCard($array)
    {
        usort($array, function($a, $b) {
            return($b->id-$a->id); 
        });
        return $array[0]->id;
    }
    
    public function call()
    {
        if(Auth::guest()) return ['success' => false];
        $game = Poker::where('status', 1)->where('user_id', $this->user->id)->first();
        
        if(is_null($game)) return ['success' => false, 'msg' => 'Не удалось найти игру!'];
        
        $game->status = 2;
        $game->save();
        
        $game->diler_cards = [];
        $game->other_cards = $this->showCards($game->other_cards);
        $game->user_cards = json_decode($game->user_cards);
        
        return ['success' => true, 'msg' => 'Продолжаем!', 'game' => $game];
        
    }
    
    private function getGame()
    {
        $game = DB::table('poker')->where('user_id', $this->user->id)->where('status', '!=', 3)->orderBy('id', 'desc')->first();
        if(!is_null($game)) {
            $game->user_cards = json_decode($game->user_cards);
            $game->diler_cards = json_decode($game->diler_cards);
            $game->other_cards = json_decode($game->other_cards);
        }
        return $game;
    }
    
    public function newGame($ante) {
        $game = [
            'user_cards' => [],
            'diler_cards' => [],
            'other_cards' => [],
            'user_id' => $this->user->id,
            'price' => $ante,
            'status' => 1
        ];
        
        $cards = $this->getCards(9);
        
        foreach($cards as $key => $card) {
            if($key < 2) $game['user_cards'][] = $card;
            if(($key >= 2) && ($key < 4)) $game['diler_cards'][] = $card;
            if($key >= 4) $game['other_cards'][] = $card;
        }
        
        $game['user_cards'] = json_encode($game['user_cards']);
        $game['diler_cards'] = json_encode($game['diler_cards']);
        $game['other_cards'] = json_encode($game['other_cards']);
        
        Poker::insert($game);
        
        $game['diler_cards'] = null;
        $game['other_cards'] = $this->showCards($game['other_cards']);
        $game['user_cards'] = json_decode($game['user_cards']);
        
        
        return $game;
    }
    
    private function showCards($array)
    {
        $array = json_decode($array);
        $list = [];
        for($i = 0; $i < 3; $i++) $list[] = $array[$i];
        return $list;
    }
    
    private function checkCombo($cards)
    {
        $cards = json_decode(json_encode($cards), true);
        # Royal Flush
        $rflash = [14, 13, 12, 11, 10];
        # Straight Flush
        $sflash = [
            [14, 2, 3, 4, 5],
            [2, 3, 4, 5, 6],
            [3, 4, 5, 6, 7],
            [4, 5, 6, 7, 8],
            [5, 6, 7, 8, 9],
            [6, 7, 8, 9, 10],
            [7, 8, 9, 10, 11],
            [8, 9, 10, 11, 12],
            [9, 10, 11, 12, 13],
            [10, 11, 12, 13, 14]
        ];
        
        # Роял Флеш
        for($i = 1; $i < 5; $i++) {
            $u = 0;
            $summ = 0;
            foreach($cards as $card) foreach($rflash as $c) if(($card['id'] == $c) && ($card['section'] == $i)) {
                $u++;
                $summ += $card['id'];
            }
            if($u == 5) return ['name' => 'Royal Flush', 'id' => 10, 'sum' => $summ];
        }
        
        # Флеш
        for($i = 1; $i < 5; $i++) {
            $u = 0;
            $summ = 0;
            foreach($cards as $card) if($card['section'] == $i) {
                $u++;
                $summ += $card['id'];
            }
            if($u >= 5) return ['name' => 'Flush', 'id' => 6, 'sum' => $summ];
        }
        
        # Стрит Флеш
        for($i = 1; $i < 5; $i++) {
            foreach($sflash as $flash) {
                $u = 0;
                $summ = 0;
                foreach($cards as $card) foreach($flash as $c) if(($card['id'] == $c) && ($card['section'] == $i)) {
                    $u++;
                    $summ += $card['id'];
                }
                if($u == 5) return ['name' => 'Straight Flush', 'id' => 9, 'sum' => $summ];
            }
         }
        
        # Стрит
        foreach($sflash as $list) {
            $u = 0;
            $summ = 0;
            foreach($cards as $card) foreach($list as $key => $id) if(!is_null($id) && ($card['id'] == $id)) {
                $list[$key] = null;
                $u++;
                $summ += $card['id'];
            }
            if($u == 5) return ['name' => 'Straight', 'id' => 5, 'sum' => $summ];
        }
        
        # Каре
        foreach($cards as $card) {
            $i = 0;
            $summ = 0;
            foreach($cards as $c) if($card['id'] == $c['id']) {
                $i++;
                $summ += $card['id'];
            }
            if($i == 4) return ['name' => 'Four of kind', 'id' => 8, 'sum' => $summ];
        }
        
        $list = [];
        
        foreach($cards as $card) {
            $found = false;
            foreach($list as $key => $item) if($item['id'] == $card['id']) {
                $found = true;
                $list[$key]['count'] += 1;
            }
            if(!$found) $list[] = [
                'id' => $card['id'],
                'count' => 1
            ];
        }

        $ar = $list;
        
        
        foreach($ar as $key => $l) {
            if($l['count'] == 2) {
                unset($ar[$key]);
                $summ = $l['id']*$l['count'];
                foreach($ar as $key => $i) if(!is_null($i) && ($i['count'] == 2)) {
                    $summ += $i['id']*$i['count'];
                    return ['name' => 'Two Pairs', 'id' => 3, 'sum' => $summ];
                } elseif(!is_null($i) && ($i['count'] == 3)) {
                    $summ += $i['id']*$i['count'];
                    return ['name' => 'Full House', 'id' => 7];
                }
                $ar = $list;
                return ['name' => 'One Pair', 'id' => 2, 'sum' => $summ];
            }
            if($l['count'] == 3) return ['name' => 'Set', 'id' => 4, 'sum' => $l['id']*$l['count']];
        }
        
        return ['name' => 'High Card', 'id' => 1];
    }
    
    private function getCards($count)
    {
        $cards = [
            ['id' => 2, 'section' => 1],
            ['id' => 2, 'section' => 2],
            ['id' => 2, 'section' => 3],
            ['id' => 2, 'section' => 4],
            ['id' => 3, 'section' => 1],
            ['id' => 3, 'section' => 2],
            ['id' => 3, 'section' => 3],
            ['id' => 3, 'section' => 4],
            ['id' => 4, 'section' => 1],
            ['id' => 4, 'section' => 2],
            ['id' => 4, 'section' => 3],
            ['id' => 4, 'section' => 4],
            ['id' => 5, 'section' => 1],
            ['id' => 5, 'section' => 2],
            ['id' => 5, 'section' => 3],
            ['id' => 5, 'section' => 4],
            ['id' => 6, 'section' => 1],
            ['id' => 6, 'section' => 2],
            ['id' => 6, 'section' => 3],
            ['id' => 6, 'section' => 4],
            ['id' => 7, 'section' => 1],
            ['id' => 7, 'section' => 2],
            ['id' => 7, 'section' => 3],
            ['id' => 7, 'section' => 4],
            ['id' => 8, 'section' => 1],
            ['id' => 8, 'section' => 2],
            ['id' => 8, 'section' => 3],
            ['id' => 8, 'section' => 4],
            ['id' => 9, 'section' => 1],
            ['id' => 9, 'section' => 2],
            ['id' => 9, 'section' => 3],
            ['id' => 9, 'section' => 4],    
            ['id' => 10, 'section' => 1],
            ['id' => 10, 'section' => 2],
            ['id' => 10, 'section' => 3],
            ['id' => 10, 'section' => 4],   
            ['id' => 11, 'section' => 1],   
            ['id' => 11, 'section' => 2],   
            ['id' => 11, 'section' => 3],   
            ['id' => 11, 'section' => 4],   
            ['id' => 12, 'section' => 1],
            ['id' => 12, 'section' => 2],
            ['id' => 12, 'section' => 3],
            ['id' => 12, 'section' => 4],   
            ['id' => 13, 'section' => 1],
            ['id' => 13, 'section' => 2],
            ['id' => 13, 'section' => 3],
            ['id' => 13, 'section' => 4],
            ['id' => 14, 'section' => 1],
            ['id' => 14, 'section' => 2],
            ['id' => 14, 'section' => 3],
            ['id' => 14, 'section' => 4]
        ];
        
        shuffle($cards);
        
        $array = [];
        
        for($i = 0; $i < $count; ) {
            $key = mt_rand(0, count($cards)-1);
            if(isset($cards[$key])) {
                $array[] = $cards[$key];
                unset($cards[$key]);
                $i++;
            }
        }
        
        return $array;
    }
}