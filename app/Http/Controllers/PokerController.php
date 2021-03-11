<?php

namespace App\Http\Controllers;

use App\User;
use App\Settings;
use App\Poker;
use Illuminate\Http\Request;
use DB;
use Auth;
use Session;
use Redis;

class PokerController extends Controller
{
    public function __construct()
    {
        $this->redis = Redis::connection();
        $this->config = Settings::first();
        if(Auth::check()) {
            $this->user = Auth::user();
            view()->share('u', $this->user);
        }
        view()->share('config', $this->config);

        $this->lang = Parent::getLang();
        view()->share('lang', $this->lang);
    }

    public function index()
    {
        parent::setTitle($this->lang['poker']['title']);
        $chat = ChatController::getMessages('poker');
        $game = Poker::where('user_id', $this->user->id)->where('status', '<', 4)->orderBy('id', 'desc')->first();
        $userCards = [];
        $userCards2 = [];
        $combo1 = null;
        $combo2 = null;
        $combo3 = null;
        if(!is_null($game)) {
            $game->cards = json_decode($game->cards);
            for($i = 0; $i < 3; $i++) $userCards[] = $game->cards[2][$i];
            for($i = 0; $i < 2; $i++) $userCards[] = $game->cards[0][$i];
            for($i = 0; $i < 5; $i++) $userCards2[] = $game->cards[2][$i];
            for($i = 0; $i < 2; $i++) $userCards2[] = $game->cards[0][$i];
            $game->bet = $game->postflop + $game->preflop + $game->preshow;
            $game->total = $game->ante + $game->blind + $game->trips + $game->bet;
            if($game->total >= 1000) $game->total = round(($game->total/1000),1).'K';

            usort($userCards, function($a, $b) {
                return($b->id-$a->id);
            });

            usort($userCards2, function($a, $b) {
                return($b->id-$a->id);
            });

            $combo1 = $this->getResults([$game->cards[0]]);
            $combo2 = $this->getResults([$userCards]);
            $combo3 = $this->getResults([$userCards2]);
        }

        return view('pages.poker', compact('chat', 'game', 'userCards', 'userCards2', 'combo1', 'combo2', 'combo3'));
    }

    public function AutoFold()
    {
        if(Auth::guest()) return ['success' => false, 'msg' => $this->lang['poker']['must_auth']];
        $game = Poker::where('user_id', $this->user->id)->orderBy('id', 'desc')->first();
        $game->is_folded = 1;
        $game->status = 4;
        $game->save();
    }

    public function start(Request $r)
    {
        if(Auth::guest()) return ['success' => false, 'msg' => $this->lang['poker']['must_auth']];
        $game = Poker::where('user_id', $this->user->id)->where('status', '<', 4)->orderBy('id', 'desc')->first();
        if(!is_null($game)) return [
            'success' => false,
            'msg' => $this->lang['poker']['lgameend']
        ];

        if($r->get('trips') < 0 || $r->get('ante') < 0 || $r->get('blind') < 0) return [
            'success' => false,
            'msg' => 'Вы забыли указать сумму ставки!'
        ];

        if($r->get('ante') < 1) return [
            'success' => false,
            'msg' => $this->lang['poker']['undsumm']
        ];

        $cards = $this->getCards([2,2,5]);

        $game = [
            'user_id' => $this->user->id,
            'cards' => json_encode($cards),
            'trips' => floor($r->get('trips')),
            'ante' => floor($r->get('ante')),
            'blind' => floor($r->get('blind')),
            'status' => 1
        ];

        $total = ($game['trips']+$game['ante']+$game['blind']);

        if($this->user->money < $total) return [
            'success' => false,
            'msg' => $this->lang['poker']['small_money']
        ];

        if(($this->user->money-$total) < $game['ante']) return [
            'success' => false,
            'msg' => $this->lang['poker']['x_ante']
        ];

        $this->user->money -= $total;
        $this->user->poker++;
        $this->user->save();

        Poker::insert($game);
        
        User::addXp($this->user->id, $total);

        if($total >= 1000) $total = number_format($total/1000, 1, '.', ' ').'K';

        # Cleaning
        // $games = Poker::where('user_id', $this->user->id)->orderBy('id', 'desc')->limit(2)->get();
        // if(count($games) == 2) Poker::where('user_id', $this->user->id)->where('id', '<', $games[1]->id)->delete();

        return [
            'success' => true,
            'data' => [
                'cards' => $cards[0],
                'shows' => $this->getShowPos($cards[0]),
                'result' => $this->getResults([$cards[0]]),
                'total' => $total,
                'balance' => number_format($this->user->money, 0, ' ', ' ')
            ],
            'status' => 1
        ];
    }

    public function checkResult(Request $r)
    {
        $game = Poker::where('user_id', $this->user->id)->where('status', '<', 4)->orderBy('id', 'desc')->first();
        if(is_null($game)) return [
            'success' => true,
            'status' => 5
        ];
        if($game->status == 1) {
            switch ($r->get('result')) {
                case 'x3':
                    $game->preflop = $game->ante*3;
                    break;
                case 'x4' :
                    $game->preflop = $game->ante*4;
                    break;
                case 'check' :
                    break;

                default:
                        return ['success' => false, 'msg' => $this->lang['poker']['timeend']];
                    break;
            }

            if($game->preflop > $this->user->money) {
                // $game->status = 4;
                // $game->save();
                return [
                    'success' => false,
                    'msg' => $this->lang['poker']['small_money']
                ];
            }

            $this->user->money -= $game->preflop;

            $game->status = 2;
            $game->save();

            $game->cards = json_decode($game->cards);

            $cards = $game->cards[2];

            $userCards = [];
            for($i = 0; $i < 2; $i++) $userCards[] = $game->cards[0][$i];
            for($i = 0; $i < 3; $i++) $userCards[] = $game->cards[2][$i];

            $total = ($game->preflop+$game->postflop+$game->preshow+$game->ante+$game->blind+$game->trips);
            if($total >= 1000) $total = number_format($total/1000, 1, '.', ' ').'K';


            $this->user->save();
            
            User::addXp($this->user->id, $game->preflop);
            return [
                'success' => true,
                'data' => [
                    'cards' => $cards,
                    'result' => $this->getResults([$userCards]),
                    'bet' => $game->preflop,
                    'total' => $total,
                    'balance' => number_format($this->user->money, 0, ' ', ' '),
                    'shows' => $this->getShowPos($userCards)
                ],
                'status' => 2
            ];
        } else if($game->status == 2) {
            if($game->preflop == 0) {
                switch ($r->get('result')) {
                    case 'x2':
                        $game->postflop = $game->ante*2;
                        break;
                    case 'check' :
                        break;
                    default:
                        return ['success' => false, 'msg' => $this->lang['poker']['timeend']];
                        break;
                }
            }

            if($game->postflop > $this->user->money) {
                // $game->status = 4;
                // $game->save();
                return [
                    'success' => false,
                    'msg' => $this->lang['poker']['small_money']
                ];
            }

            $this->user->money -= $game->postflop;
            $total = ($game->preflop+$game->postflop+$game->preshow+$game->ante+$game->blind+$game->trips);

            $game->value = $total;

            $game->status = 3;
            $game->save();

            $game->cards = json_decode($game->cards);
            $cards = $game->cards[2];

    
            if($total >= 1000) $total = number_format($total/1000, 1, '.', ' ').'K';

            $this->user->save();
            
            User::addXp($this->user->id, $game->postflop);

            $userCards = [];
            for($i = 0; $i < 5; $i++) $userCards[] = $game->cards[2][$i];
            for($i = 0; $i < 2; $i++) $userCards[] = $game->cards[0][$i];

            return [
                'success' => true,
                'data' => [
                    'cards' => $cards,
                    'result' => $this->getResults([$game->cards[2], $game->cards[0]]),
                    'bet' => $game->preflop+$game->postflop,
                    'total' => $total,
                    'balance' => number_format($this->user->money, 0, ' ', ' '),
                    'shows' => $this->getShowPos($userCards)
                ],
                'status' => 3
            ];
        } else if($game->status == 3) {
            $bet2 = 0;
            if($game->preflop == 0 && $game->postflop == 0) {
                switch ($r->get('result')) {
                    case 'x1':
                        $game->preshow = $game->ante;
                        $bet2 = $game->preshow;
                        break;
                    case 'fold':
                        $game->is_folded = 1;
                        $game->status = 4;
                        $game->save();

                        return [
                            'success' => true,
                            'status' => 5
                        ];
                        break;
                    default:
                        return ['success' => false, 'msg' => $this->lang['poker']['timeend']];
                        break;
                }
            }

            if($game->preshow > $this->user->money) {
                // $game->status = 4;
                // $game->save();
                return [
                    'success' => false,
                    'msg' => $this->lang['poker']['small_money']
                ];
            }

            $this->user->money -= $game->preshow;
            $balance2 = $this->user->money;
            $total2 = ($game->preflop+$game->postflop+$game->preshow+$game->ante+$game->blind+$game->trips);


            // $game->save();

            $game->cards = json_decode($game->cards);
            $cards = [];
            for($i = 0; $i < 2; $i++) $cards[] = $game->cards[1][$i];
            for($i = 0; $i < 5; $i++) $cards[] = $game->cards[2][$i];

            $userResult = $this->getResults([$game->cards[0], $game->cards[2]]);
            $game->combo = $userResult['id'];
            $game->summ = $userResult['card'];
            $dilerResult = $this->getResults([$game->cards[1], $game->cards[2]]);

            $is_win = false;
            $suck = false;
            $nGame = false;
            $gameStatus = 0;

            if($dilerResult['id'] < $userResult['id']) $is_win = true;
            if($dilerResult['id'] == $userResult['id'] && $userResult['card'] > $dilerResult['card']) $is_win = true;
            if($dilerResult['id'] == $userResult['id'] && $userResult['card'] == $dilerResult['card']) {
                // $suck = true;
                $uC = $game->cards[0];
                $dC = $game->cards[1];
                usort($uC, function($a, $b) {
                    return($b->id - $a->id);
                });
                usort($dC, function($a, $b) {
                    return($b->id - $a->id);
                });

                if($uC[0]->id > $dC[0]->id) {
                    $is_win = true;
                } else if($uC[0]->id == $dC[0]->id) {
                    $suck = true;
                } else {
                    $is_win = false;
                }
            }

            if($dilerResult['id'] > 2) {
                $nGame = true;
            } elseif($dilerResult['id'] == 2 && $dilerResult['card'] >= 4) {
                $nGame = true;
            }

            // // if(!$nGame) if(!$suck) $game->ante = 0;
            // if(!$nGame) if(!$suck) {
            //     $game->preshow *= 2;
            //     $game->preflop *= 2;
            //     $game->postflop *= 2;
            //     $game->ante *= 1;
            // }

            if($userResult['id'] == 10) {
                $game->trips *= 101;
                if(!$suck) $game->blind *= 501;
            } elseif($userResult['id'] == 9) {
                $game->trips *= 41;
                if(!$suck) $game->blind *= 51;
            } elseif($userResult['id'] == 8) {
                $game->trips *= 21;
                if(!$suck) $game->blind *= 11;
            } elseif($userResult['id'] == 7) {
                $game->trips *= 8;
                if(!$suck) $game->blind *= 4;
            } elseif($userResult['id'] == 6) {
                $game->trips *= 7;
                if(!$suck) $game->blind *= 2.5;
            } elseif($userResult['id'] == 5) {
                $game->trips *= 6;
                if(!$suck) $game->blind *= 2;
            } elseif($userResult['id'] == 4) {
                $game->trips *= 4;
            } else {
                $game->trips = 0;
                // if(!$suck && $nGame) $game->blind = 0;
            }

            if(!$suck && !$is_win && $nGame) {
                $game->blind = 0;
                $game->ante = 0;
                $game->preshow = 0;
                $game->preflop = 0;
                $game->postflop = 0;
                $gameStatus = -1;
            }

            if($suck) {
                // Ничья
                $game->blind *= 1;
                $game->ante *= 1;
                $game->preshow *= 1;
                $game->preflop *= 1;
                $game->postflop *= 1;
            }

            if($is_win && !$suck) {
                if(!$nGame) $game->ante *= 1; else $game->ante *= 2;
                $game->preshow *= 2;
                $game->preflop *= 2;
                $game->postflop *= 2;
            }

            if(!$nGame) $gameStatus = 1;
            if($suck) $gameStatus = 2;

            if($gameStatus == 0) 
            {
                PromoController::ref(floor($game->preflop+$game->postflop+$game->preshow+$game->ante+$game->blind+$game->trips), $this->user);
                // PromoController::ref(floor, $this->user);
                $game->is_win = 1;
            }

            $game->status = 4;
            $total = ($game->preflop+$game->postflop+$game->preshow+$game->ante+$game->blind+$game->trips);
            $game->winner_value = $total;
            $game->cards = json_encode($game->cards);
            $game->save();

            usort($cards, function($a, $b) {
                return($b->id-$a->id);
            });

            $this->user->money += $total;
            if($total >= 1000) $total = number_format($total/1000, 1, '.', ' ').'K';

            $this->user->save();
            User::addXp($this->user->id, $game->preshow);

            AchievementController::checkAchievement($this->user, $this->redis, $this->lang['achievement_unlock']);


            return [
                'success' => true,
                'data' => [
                    'user' => $userResult,
                    'dealer' => $dilerResult,
                    'win' => $is_win,
                    'cards' => $cards,
                    'dc' => json_decode($game->cards)[1],
                    'bet' => ($game->preflop+$game->postflop+$game->preshow),
                    'bet2' => $bet2,
                    'ante' => $game->ante,
                    'blind' => $game->blind,
                    'trips' => $game->trips,
                    'total' => $total,
                    'total2' => $total2,
                    'gameStatus' => $gameStatus,
                    'balance2' => $balance2,
                    'balance' => number_format($this->user->money, 0, ' ', ' ')
                ],
                'status' => 4
            ];
        } else {
            return ['success' => false, 'msg' => $this->lang['poker']['gameend'].' ('.$game->status.')'];
        }
    }

    public function getCards($values)
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

        $array = [];
        foreach($values as $key => $value) {
            $array[$key] = [];
            for($i = 0; $i < $value; ) {
                $cardKey = mt_rand(0, count($cards)-1);
                if(isset($cards[$cardKey]) && !is_null($cards[$cardKey])) {
                    $array[$key][] = $cards[$cardKey];
                    unset($cards[$cardKey]);
                    $i++;
                }
            }
        }

        return $array;
    }

    public function getResults($arrays)
    {
        $array = [];
        foreach($arrays as $a) {
            $a = json_decode(json_encode($a), true);
            foreach($a as $card) $array[] = $card;
        }

        usort($array, function($a, $b) {
            return($b['id']-$a['id']);
        });

        # Royal Flush
        for($i = 1; $i < 5; $i++) {
            $count = 0;
            $list = [10, 11, 12, 13, 14];
            $used = [];
            foreach($array as $card) foreach($list as $key => $id) if(!is_null($list[$key]) && ($card['id'] == $id && $card['section'] == $i)) {
                unset($list[$key]);
                $count++;
                $used[] = $card;
            }
            if($count >= 5) return [
                'id' => 10,
                'name' => $this->lang['poker']['rflush'],
                'card' => 60,
                'used' => $this->usedCards($used, $array)
            ];
        }

        #Straight Flush
        for($i = 1; $i < 5; $i++) {
            $count = 0;
            $list = [
                [14, 2, 3, 4, 5],
                [2, 3, 4, 5, 6],
                [3, 4, 5, 6, 7],
                [4, 5, 6, 7, 8],
                [5, 6, 7, 8, 9],
                [6, 7, 8, 9, 10],
                [7, 8, 9, 10, 11],
                [8, 9, 10, 11, 12],
                [9, 10, 11, 12, 13],
                [10, 11, 12, 13, 14],
            ];
            foreach($list as $key => $ids) {
                $count = 0;
                $sum = 0;
                $used = [];
                foreach($array as $card) foreach($ids as $IDKey => $id) if(!is_null($ids[$IDKey]) && ($card['id'] == $id && $card['section'] == $i)) {
                    // unset($list[$key][$IDKey]);
                    unset($ids[$IDKey]);
                    $count++;
                    $used[] = $card;
                    if($key == 0 && $card['id'] == 14) $sum += 1; else $sum += $card['id'];
                }
                if($count >= 5) return [
                    'name' => $this->lang['poker']['sflush'],
                    'id' => 9,
                    'card' => $sum,
                    'used' => $this->usedCards($used, $array)
                ];
            }
        }

        # Four of Kind
        for($i = 2; $i < 15; $i++) {
            $count = 0;
            $used = [];
            foreach($array as $key => $card) if($card['id'] == $i) {
                $count++;
                $found = false;
                foreach($used as $n) if($card['id'] == $n) $found = true;
                if(!$found) $used[] = $card;

            }
            if($count >= 4) return [
                'name' => $this->lang['poker']['fok'],
                'id' => 8,
                'card' => ($i*4),
                'used' => $this->usedCards($used, $array)
            ];
        }

        # Full House
        $cards = [];

        for($i = 2; $i < 15; $i++) {
            if(!isset($cards[$i])) $cards[$i] = [];
            foreach($array as $card) if($card['id'] == $i) $cards[$i][] = $card;
        }

        $cards = array_reverse($cards);

        $list = [];
        $sum = 0;
        $take = 0;
        while($take < 24) {
            foreach($cards as $key => $card) {

                $take++;
                $items = null;
                if(count($card) == 2 && count($list) == 0) $items = $card;
                if(count($card) == 3 && count($list) == 2) $items = $card;

                if(!is_null($items)) {
                    unset($cards[$key]);
                    $count = 2;
                    foreach($items as $c) {
                        $list[] = $c;
                        $sum += $c['id'];
                    }
                }
            }
        }

        if(count($list) == 5) return [
            'name' => $this->lang['poker']['fullhouse'],
            'id' => 7,
            'card' => $sum,
            'used' => $this->usedCards($list, $array)
        ];

        # Flush
        for($i = 1; $i < 4; $i++) {
            $count = 0;
            $sum = 0;
            $used = [];
            foreach($array as $card) if($card['section'] == $i && $count < 6) {
                $count++;
                $sum += $card['id'];
                $used[] = $card;
            }
            if($count >= 5) return [
                'name' => $this->lang['poker']['flush'],
                'id' => 6,
                'card' => $sum,
                'used' => $this->usedCards($used, $array)
            ];
        }

        # Straight
        for($i = 1; $i < 2; $i++) {
            $count = 0;
            $list = [
                [14, 2, 3, 4, 5],
                [2, 3, 4, 5, 6],
                [3, 4, 5, 6, 7],
                [4, 5, 6, 7, 8],
                [5, 6, 7, 8, 9],
                [6, 7, 8, 9, 10],
                [7, 8, 9, 10, 11],
                [8, 9, 10, 11, 12],
                [9, 10, 11, 12, 13],
                [10, 11, 12, 13, 14],
            ];
            foreach($list as $key => $ids) {
                $count = 0;
                $sum = 0;
                $used = [];
                foreach($array as $card) foreach($ids as $IDKey => $id) if(!is_null($ids[$IDKey]) && $card['id'] == $id) {
                    unset($ids[$IDKey]);
                    $count++;
                    $used[] = $card;
                    if($key == 0 && $card['id'] == 14) $sum += 1; else $sum += $card['id'];
                }
                if($count >= 5) return [
                    'name' => $this->lang['poker']['straight'],
                    'id' => 5,
                    'card' => $sum,
                    'used' => $this->usedCards($used, $array)
                ];
            }
        }

        # Three of Kind
        for($i = 2; $i < 15; $i++) {
            $count = 0;
            $used = [];
            foreach($array as $card) if($card['id'] == $i) {
                $count++;
                $used[] = $card;
            }
            if($count >= 3) {
                return [
                    'name' => $this->lang['poker']['tok'],
                    'id' => 4,
                    'card' => ($i*3),
                    'used' => $this->usedCards($used, $array)
                ];
            }
        }

        # Two Pairs
        $cards = [];

        for($i = 2; $i < 15; $i++) {
            if(!isset($cards[$i])) $cards[$i] = [];
            foreach($array as $card) if($card['id'] == $i) $cards[$i][] = $card;
        }

        $cards = array_reverse($cards);

        $list = [];
        $sum = 0;
        $take = 0;
        $key = 0;
        while($take < 24) {
            foreach($cards as $key => $card) {
                if($key == 2) $take = 24;

                $take++;
                $items = null;
                if(count($card) >= 2 && count($list) == 0) $items = $card;
                if(count($card) >= 2 && count($list) == 2) $items = $card;

                if(!is_null($items)) {
                    $key++;
                    unset($cards[$key]);
                    for($i = 0; $i < 2; $i++) {
                        $list[] = $items[$i];
                        $sum += $items[$i]['id'];
                    }
                }
            }
        }

        if(count($list) == 4) return [
            'name' => $this->lang['poker']['tp'],
            'id' => 3,
            'card' => $sum,
            'used' => $this->usedCards($list, $array)
        ];

        # One Pair
        for($i = 2; $i < 15; $i++) {
            $count = 0;
            $used = [];
            foreach($array as $card) if($card['id'] == $i) {
                $count++;
                $used[] = $card;
            }
            if($count >= 2) return [
                'name' => $this->lang['poker']['op'],
                'card' => ($i*2),
                'id' => 2,
                'used' => $this->usedCards($used, $array)
            ];
        }

        usort($array, function($a, $b) {
            return($b['id']-$a['id']);
        });
        $high = $array[0];

        return [
            'name' => $this->lang['poker']['hc'],
            'id' => 1,
            'used' => $this->usedCards([$high], $array),
            'card' => $high['id']
        ];

    }

    public function getShowPos($cards)
    {
        $list = [];
        for($i = 0; $i < count($cards); $i++) {
            $cList = [];
            for($x = 0; $x < ($i+1); $x++) $cList[] = $cards[$x];
            $result = $this->getResults([$cList]);
            $list[] = [
                'cards' => $result['used'],
                'result' => $result
            ];
        }

        return $list;
    }

    public function usedCards($used, $cards)
    {
        foreach($cards as $key => $card) foreach($used as $u) if(!is_null($card) && $card['id'] == $u['id'] && $card['section'] == $u['section']) unset($cards[$key]);

        usort($used, function($a, $b) {
            return($b['id']-$a['id']);
        });

        foreach($used as $key => $c) $used[$key]['used'] = 1;

        $list = [];
        foreach($cards as $c) if(!is_null($c)) $list[] = $c;

        usort($list, function($a, $b) {
            return($b['id']-$a['id']);
        });

        $key = 0;
        if(count($used) < 5) for($i = count($used); $i < 5; $i++) {
            if($key < count($list)) {
                $used[] = $list[$key];
                $key++;
            }
        }

        return $used;
    }

    public function test()
    {
        $array = [
            ['id' => 11, 'section' => mt_rand(1,4)],
            ['id' => 11, 'section' => mt_rand(1,4)],
            ['id' => 3, 'section' => mt_rand(1,4)],
            ['id' => 3, 'section' => mt_rand(1,4)],
            ['id' => 4, 'section' => mt_rand(1,4)],
            ['id' => 14, 'section' => mt_rand(1,4)],
            ['id' => 14, 'section' => mt_rand(1,4)],
        ];

        # Two Pairs
        $cards = [];

        for($i = 2; $i < 15; $i++) {
            if(!isset($cards[$i])) $cards[$i] = [];
            foreach($array as $card) if($card['id'] == $i) $cards[$i][] = $card;
        }

        $cards = array_reverse($cards);

        $list = [];
        $sum = 0;
        $take = 0;
        $key = 0;
        while($take < 24) {
            foreach($cards as $key => $card) {
                if($key == 2) $take = 24;

                $take++;
                $items = null;
                if(count($card) >= 2 && count($list) == 0) $items = $card;
                if(count($card) >= 2 && count($list) == 2) $items = $card;

                if(!is_null($items)) {
                    $key++;
                    unset($cards[$key]);
                    for($i = 0; $i < 2; $i++) {
                        $list[] = $items[$i];
                        $sum += $items[$i]['id'];
                    }
                }
            }
        }

        if(count($list) == 4) return [
            'name' => 'Two Pairs',
            'id' => 3,
            'card' => $sum,
            'used' => $this->usedCards($list, $array)
        ];

    }
    
    public function profitTest($ante_, $trips_) {
        $min = 100;
        $max = 0;
        $list = [];
        for($u = 0; $u < 100; $u++)
        {
            $value = 0;
            $winner = 0;
            for($i = 0; $i < 100; $i++) {
                $blind = $ante_;
                $ante = $ante_;
                $trips = $trips_;
                $value += ($ante_+$blind+$trips_);
                $cards = $this->getCards([2,2,5]);

                $user = $this->getResults([$cards[0], $cards[2]]);
                $dealer = $this->getResults([$cards[1], $cards[2]]);

                $is_win = false;
                $suck = false;
                $nGame = false;
                $gameStatus = 0;

                $preValue = 0;

                if($dealer['id'] < $user['id']) $is_win = true;
                if($dealer['id'] == $user['id'] && $user['card'] > $dealer['card']) $is_win = true;
                if($dealer['id'] == $user['id'] && $user['card'] == $dealer['card']) {
                    // $suck = true;
                    $uC = $cards[0];
                    $dC = $cards[1];
                    usort($uC, function($a, $b) {
                        return($b['id'] - $a['id']);
                    });
                    usort($dC, function($a, $b) {
                        return($b['id'] - $a['id']);
                    });

                    if($uC[0]['id'] > $dC[0]['id']) {
                        $is_win = true;
                    } else if($uC[0]['id'] == $dC[0]['id']) {
                        $suck = true;
                    } else {
                        $is_win = false;
                    }
                }

                if($dealer['id'] > 2) {
                    $nGame = true;
                } elseif($dealer['id'] == 2 && $dealer['card'] >= 4) {
                    $nGame = true;
                }

                // // if(!$nGame) if(!$suck) $ante = 0;
                // if(!$nGame) if(!$suck) {
                //     $preshow *= 2;
                //     $preflop *= 2;
                //     $postflop *= 2;
                //     $ante *= 1;
                // }

                if($user['id'] == 10) {
                    $trips *= 101;
                    if(!$suck) $blind *= 501;
                } elseif($user['id'] == 9) {
                    $trips *= 41;
                    if(!$suck) $blind *= 51;
                } elseif($user['id'] == 8) {
                    $trips *= 21;
                    if(!$suck) $blind *= 11;
                } elseif($user['id'] == 7) {
                    $trips *= 8;
                    if(!$suck) $blind *= 4;
                } elseif($user['id'] == 6) {
                    $trips *= 7;
                    if(!$suck) $blind *= 2.5;
                } elseif($user['id'] == 5) {
                    $trips *= 6;
                    if(!$suck) $blind *= 2;
                } elseif($user['id'] == 4) {
                    $trips *= 4;
                } else {
                    $trips = 0;
                    // if(!$suck && $nGame) $blind = 0;
                }

                if(!$suck && !$is_win && $nGame) {
                    $blind = 0;
                    $ante = 0;
                    $preValue = 0;
                    $gameStatus = -1;
                }

                if($suck) {
                    // Ничья
                    $blind *= 1;
                    $ante *= 1;
                    $preValue *= 1;
                }

                if($is_win && !$suck) {
                    if(!$nGame) $ante *= 1; else $ante *= 2;
                    $preValue *= 2;
                }

                if(!$nGame) $gameStatus = 1;
                if($suck) $gameStatus = 2;

    //            echo $ante.'<br>';
    //            echo $blind.'<br>';
    //            echo $trips.'<br>';

                $winner += ($ante+$blind+$trips);

    //            if($is_win) $won++; else $lose++;

    //            return $user;
            }
            $profit = round(100-($winner/$value)*100, 2);
            if($profit > $max) $max = $profit;
            if($profit < $min) $min = $profit;
            $list[] = $profit;
        }
        
//        echo 'Won - '.$value.' /'.' Bets - '.$before.' / Profit - ('.($value-$before).')<br>';
//        echo 'Wins Count - '.$won.'/ Lose count - '.$lose.' Win percent - ('.number_format(($won/10000)*100, 2, '.', '.').'%)';
        $avg = 0;
        foreach($list as $profit) $avg += $profit;
        $avg = round($avg/count($list), 2);
        echo 'MIN : '.$min.'% MAX : '.$max.'% AVG : '.$avg.'%';
    }
}
