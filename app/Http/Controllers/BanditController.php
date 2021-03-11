<?php namespace App\Http\Controllers;

use DB;
use App\Bandit;
use App\User;
use App\Settings;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Redis;

class BanditController extends Controller
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

        $this->redis = Redis::connection();
    }

    public function index()
    {
        parent::setTitle($this->lang['bandit']['title']);
        $chat = ChatController::getMessages('bandit');
        return view('pages.bandit', compact('chat'));
    }

    public function PayTable()
    {
        parent::setTitle('Pay Table');
        $list = DB::table('bandit_images')->orderBy('type', 'asc')->get();
        foreach($list as $key => $item) {
            $list[$key]->multiplier = json_decode($item->multiplier);
            if($item->type == 2) $list[$key]->games = json_decode($this->config->bandit_free_spins_count);
        }
        $chat = ChatController::getMessages('bandit');
        return view('pages.paytable', compact('list', 'chat'));
    }

    public function paytable2($lines, $bet)
    {
        parent::setTitle('Pay Table');
        $list = DB::table('bandit_images')->orderBy('type', 'asc')->get();
        foreach($list as $key => $item) {
            $list[$key]->multiplier = json_decode($item->multiplier);
            if($lines == 2) $list[$key]->multiplier = json_decode($item->multiplier2);
            if($lines == 3) $list[$key]->multiplier = json_decode($item->multiplier3);
            if($lines == 4) $list[$key]->multiplier = json_decode($item->multiplier4);
            if($lines == 5) $list[$key]->multiplier = json_decode($item->multiplier5);
            if($lines == 6) $list[$key]->multiplier = json_decode($item->multiplier6);
            if($lines == 7) $list[$key]->multiplier = json_decode($item->multiplier7);
            if($lines == 8) $list[$key]->multiplier = json_decode($item->multiplier8);
            if($lines == 9) $list[$key]->multiplier = json_decode($item->multiplier9);
            if($item->type == 2) $list[$key]->games = json_decode($this->config->bandit_free_spins_count);
            $list[$key]->bets = [($list[$key]->multiplier[0]*$bet),($list[$key]->multiplier[1]*$bet),($list[$key]->multiplier[2]*$bet)];
            foreach($list[$key]->bets as $i => $b) if($b > 1000000) {
                $b = round($b/1000000,1).'M';
                $list[$key]->bets[$i] = $b;
            } elseif($b >= 1000) {
                $b = round($b/1000,1).'K';
                $list[$key]->bets[$i] = $b;
            }
        }

        $chat = ChatController::getMessages('bandit');
        return view('pages.paytable2', compact('list', 'chat', 'bet'));
    }

    public function checkFree()
    {
        if($this->user->bandit_spins > 0) return ['success' => true];
        return ['success' => false];
    }

    public function newGame(Request $r)
    {
        if(Auth::guest()) return ['success' => false, 'msg' => $this->lang['bandit']['mbauth']];

        DB::table('bandit')->where('user_id', $this->user->id)->update(['status' => 3]);

        if($this->user->bandit_spins > 0) return $this->newFreeGame();

        $lastGame = DB::table('bandit')->where('user_id', $this->user->id)->orderBy('id', 'desc')->first();
        if(!is_null($lastGame) && ($lastGame->created_at > Carbon::now('MSK'))) return [
            'success' => false,
            'msg' => $this->lang['bandit']['lgameend']
        ];

        if(floatval($r->get('lines')) < 1 || floatval($r->get('lines')) > 9) return ['success' => false, 'msg' => $this->lang['bandit']['cflines']];

        if(floatval($r->get('bet')) < 1) return [
            'success' => false,
            'msg' => $this->lang['bandit']['cfbet']
        ];

        if(floatval($r->get('bet')) < ($this->config->bandit_min_bet*floatval($r->get('lines')))) return [
            'success' => false,
            'msg' => $this->lang['bandit']['minbet'].($this->config->bandit_min_bet*floatval($r->get('lines')))
        ];

        if(($this->config->bandit_max_bet != 0) && (floatval($r->get('bet')) > $this->config->bandit_max_bet)) return [
            'success' => false,
            'msg' => $this->lang['bandit']['maxbet'].$this->config->bandit_max_bet
        ];


        $bet = $r->get('bet')*$r->get('lines');

        if($bet > $this->user->money) return [
            'success' => false,
            'msg' => $this->lang['bandit']['small_money']
        ];

        $this->user->money -= $bet;
        $this->user->save();

        User::addXp($this->user->id, $bet);

        return $this->createNewGame(floatval($r->get('lines')), floatval($r->get('bet')), 'notfree', $r->get('is_auto'));
    }

    public function createNewGame($linesCount, $bet, $type, $is_auto)
    {
        $freeSpinsCount = $this->user->bandit_spins;
        $freeSpinsImage = DB::table('bandit_images')->where('type', 2)->first();
        $balance = $this->user->money;

        $images = DB::table('bandit_images')->where('type', '!=', 2)->orderBy('id', 'desc')->get();

        #Generate Lines
        $resArray = [];
        $percent = $this->config->bandit_winpercent;
        if($linesCount == 2) $percent = $this->config->bandit_winpercent2;
        if($linesCount == 3) $percent = $this->config->bandit_winpercent3;
        if($linesCount == 4) $percent = $this->config->bandit_winpercent4;
        if($linesCount == 5) $percent = $this->config->bandit_winpercent5;
        if($linesCount == 6) $percent = $this->config->bandit_winpercent6;
        if($linesCount == 7) $percent = $this->config->bandit_winpercent7;
        if($linesCount == 8) $percent = $this->config->bandit_winpercent8;
        if($linesCount == 9) $percent = $this->config->bandit_winpercent9;
        for($i = 0; $i < floor($percent); $i++) $resArray[] = true;
        for($i = 0; $i < floor(100-$percent); $i++) $resArray[] = false;
        shuffle($resArray);

        $lines = $this->getLines($resArray[mt_rand(0, count($resArray)-1)], $linesCount);

        $isFree = $this->getFreeSpins();
        if($isFree[0]) {
            $freeList = [3,4,5];
            $freeList = $freeList[$isFree[1]];
            if(!is_null($freeSpinsImage)) {
                $mList = json_decode($freeSpinsImage->multiplier, true);
                $m2 = $mList[$isFree[1]];
                for($i = 0; $i < $freeList; ) {
                    $lineKey = mt_rand(0, 4);
                    $itemKey = mt_rand(0, 2);
                    if($lines[$lineKey][$itemKey]['id'] != $freeSpinsImage->id && $lines[$lineKey][$itemKey]['id'] != null) {
                        $lines[$lineKey][$itemKey] = [
                            'id' => $freeSpinsImage->id,
                            'url' => $freeSpinsImage->url,
                            'type' => $freeSpinsImage->type
                        ];
                        $i++;
                    }
                }

                // Add Free Spins from user
                $freeSpins = 5;
                $countList = json_decode($this->config->bandit_free_spins_count);
                $this->user->bandit_spins += $countList[$isFree[1]];
                $this->user->save();
            }
        }

        $gameStatus = 3;
        $isGamed = ['id' => 0];

        $gameResActive = false;
        if(!$isFree[0]) {
            $list = [];
            for($i = 0; $i < $this->config->bandit_bonus; $i++) $list[] = true;
            for($i = 0; $i < (100-$this->config->bandit_bonus); $i++) $list[] = false;
            $gameResActive = $list[mt_rand(0, count($list)-1)];
        }

        if($gameResActive) {
            $list = [];
            for($i = 0; $i < $this->config->bandit_quest; $i++) $list[] = 1;
            for($i = 0; $i < $this->config->bandit_mgame; $i++) $list[] = 2;
            for($i = 0; $i < $this->config->bandit_towers; $i++) $list[] = 3;
            shuffle($list);
            $key = mt_rand(0, count($list)-1);
            if(!is_null($list[$key])) {
                $isGamed['id'] = $list[$key];
                $gameStatus = 1;
            }
        }

        $gameRes = false;
        if($isGamed['id'] != 0) $gameRes = true;


        $m2 = 0;

        $lineList = [];

        $winners = $this->getWinnerLines($lines, $images, $linesCount);
        $lines = $winners['lines'];

        if($gameRes && count($winners['list']) > 0) {
            $keysList = [];
            foreach($lines as $line => $items) foreach($items as $item_id => $item) {
                if(!isset($item['line_id'])) $keysList[] = [
                    'line' => $line,
                    'item_id' => $item_id
                ];
            }
            $key = $keysList[mt_rand(0, count($keysList)-1)];
            $lines[$key['line']][$key['item_id']] = [
                'id' => null,
                'url' => 'http://dotaregal.com/assets/frontend/images/slot/bonus/none.png',
                'type' => null
            ];
            // return $lines[$key['line']];
        } else {
            $isGamed['id'] = null;
            $gameStatus = 3;
            $gameRes = false;
        }

        for($i = 0; $i < count($lines); $i++) {
            $key = ($i+1)*6;
            for($u = 0; $u < $key; $u++) {
                $itemKey = mt_rand(0, count($images)-1);
                $lineList[$i][] = [
                    'id' => $images[$itemKey]->id,
                    'url' => $images[$itemKey]->url
                ];
            }
        }
        foreach($lines as $line => $items) foreach($items as $item) $lineList[$line][] = $item;

        // FIXME: Находим множитель.

        $m = $m2;
        $price = 0;
        foreach($winners['list'] as $item) {
            $image = DB::table('bandit_images')->where('id', $item['id'])->first();
            $multiplier = 0;
            if(!is_null($image)) {
                $list = json_decode($image->multiplier);
                if($linesCount == 2) $list = json_decode($image->multiplier2);
                if($linesCount == 3) $list = json_decode($image->multiplier3);
                if($linesCount == 4) $list = json_decode($image->multiplier4);
                if($linesCount == 5) $list = json_decode($image->multiplier5);
                if($linesCount == 6) $list = json_decode($image->multiplier6);
                if($linesCount == 7) $list = json_decode($image->multiplier7);
                if($linesCount == 8) $list = json_decode($image->multiplier8);
                if($linesCount == 9) $list = json_decode($image->multiplier9);
                switch ($item['count']) {
                    case 3:
                        $multiplier = $list[0];
                        break;
                    case 4:
                        $multiplier = $list[1];
                        break;
                    case 5:
                        $multiplier = $list[2];
                        break;
                }
            }

            $m += $multiplier;
        }

        $price += floor($bet*$m);

        // $m = $m*floatval($bet/100);
        $this->user->slot_machine++;
        $this->user->money += $price;
        PromoController::ref(floor($price), $this->user);
        $this->user->save();

        switch ($type) {
            case 'free':
                $typeID = 1;
                break;
            case 'notfree':
                $typeID = 0;
                break;
        }

        if($isGamed['id'] == 1) {
            $list = [];
            $list[] = ['id' => null, 'url' => 'http://dotaregal.com/assets/frontend/images/slot/bonus/x2.png', 'type' => null];
            $list[] = ['id' => null, 'url' => 'http://dotaregal.com/assets/frontend/images/slot/bonus/x3.png', 'type' => null];
            $list[] = ['id' => null, 'url' => 'http://dotaregal.com/assets/frontend/images/slot/bonus/x4.png', 'type' => null];
            $list[] = ['id' => null, 'url' => 'http://dotaregal.com/assets/frontend/images/slot/free.png', 'type' => null];
            $images = DB::table('bandit_images')->where('type', 0)->get();
            for($i = 0; $i < 11; $i++) {
                $key = mt_rand(0, count($images)-1);
                $list[] = [
                    'id' => $images[$key]->id,
                    'url' => $images[$key]->url,
                    'type' => $images[$key]->type
                ];
            }
            shuffle($list);

            $fakeLines = [];
            $itemsKey = 0;
            for($i = 0; $i < 5; $i++) {
                $fakeLines[$i] = [];
                for($x = 0; $x < 3; $x++) {
                    $fakeLines[$i][$x] = $list[$itemsKey];
                    $itemsKey++;
                }
            }
            $isGamed['lines'] = $fakeLines;
        }

        $BONUS = 0;
        if($gameStatus == 1) $BONUS = 1;

        DB::table('bandit')->insert([
            'user_id' => $this->user->id,
            'value' => $bet,
            'lines' => floatval($linesCount),
            'winner_value' => $price,
            'multiplier' => $m,
            'is_free' => $typeID,
            'created_at' => Carbon::now('MSK')->addSecond(2),
            'type' => 'default',
            'status' => $gameStatus,
            'values' => '[]',
            'bonus' => $BONUS,
            'wins_lines' => count($winners['counts']),
            'max_count' => json_encode($winners['counts']),
            'is_auto' => $is_auto
        ]);

        if($price >= 1000) $price = round($price/1000, 1).'K';

        // # Cleaning
        // $games = DB::table('bandit')->where('user_id', $this->user->id)->orderBy('id', 'desc')->limit(2)->get();
        // if(count($games) == 2) DB::table('bandit')->where('user_id', $this->user->id)->where('id', '<', $games[1]->id)->delete();

        AchievementController::checkAchievement($this->user, $this->redis, $this->lang['achievement_unlock']);

        return [
            'success' => true,
            'lines' => $lineList,
            'winners' => $winners['list'],
            'balance' => number_format($balance, 0, ' ', ' '),
            'win' => $price,
            'newbalance' => number_format($this->user->money, 0, ' ', ' '),
            'type' => $type,
            'freeCount' => $this->user->bandit_spins-1,
            'freeCount2' => $freeSpinsCount,
            'm' => $m,
            'keys' => $winners['keys'],
            'game' => $isGamed
        ];
    }

    public function newFreeGame()
    {
        $l = DB::table('bandit')->where('user_id', $this->user->id)->orderBy('id', 'desc')->first();

        $this->user->bandit_spins--;
        $this->user->save();

        if(!is_null($l)) $value = floor($l->value*$l->lines); else $value = 100;
        if($value >= 1000) $value = round($value/1000, 1).'K';

        if(!is_null($l)) $bet = $l->value; else $bet = 100;
        if($bet >= 1000) $bet = round($bet/1000, 1).'K';

        if(!is_null($l)) $game = $this->createNewGame($l->lines, $l->value, 'free', 0); else $game = $this->createNewGame(1, 100, 'free', 0);
        if(!is_null($l)) $game['linesCount'] = $l->lines; else $game['linesCount'] = 1;
        $game['bet'] = $bet;
        $game['value'] = $value;

        return $game;
    }

    private function getWinnerLines($lines, $images, $linesCount)
    {
        $lineIDs = [];
        $lineKeys = [];
        $counts = [];
        $list = [
            [ // Line 1
                ['line' => 0, 'pos' => 1],
                ['line' => 1, 'pos' => 1],
                ['line' => 2, 'pos' => 1],
                ['line' => 3, 'pos' => 1],
                ['line' => 4, 'pos' => 1]
            ],
            [ // Line 2
                ['line' => 0, 'pos' => 0],
                ['line' => 1, 'pos' => 0],
                ['line' => 2, 'pos' => 0],
                ['line' => 3, 'pos' => 0],
                ['line' => 4, 'pos' => 0]
            ],
            [ // Line 3
                ['line' => 0, 'pos' => 2],
                ['line' => 1, 'pos' => 2],
                ['line' => 2, 'pos' => 2],
                ['line' => 3, 'pos' => 2],
                ['line' => 4, 'pos' => 2]
            ],
            [ // Line 4
                ['line' => 0, 'pos' => 0],
                ['line' => 1, 'pos' => 1],
                ['line' => 2, 'pos' => 2],
                ['line' => 3, 'pos' => 1],
                ['line' => 4, 'pos' => 0]
            ],
            [ // Line 5
                ['line' => 0, 'pos' => 2],
                ['line' => 1, 'pos' => 1],
                ['line' => 2, 'pos' => 0],
                ['line' => 3, 'pos' => 1],
                ['line' => 4, 'pos' => 2]
            ],
            [ // Line 6
                ['line' => 0, 'pos' => 0],
                ['line' => 1, 'pos' => 0],
                ['line' => 2, 'pos' => 1],
                ['line' => 3, 'pos' => 0],
                ['line' => 4, 'pos' => 0]
            ],
            [ // Line 7
                ['line' => 0, 'pos' => 2],
                ['line' => 1, 'pos' => 2],
                ['line' => 2, 'pos' => 1],
                ['line' => 3, 'pos' => 2],
                ['line' => 4, 'pos' => 2]
            ],
            [ // Line 8
                ['line' => 0, 'pos' => 1],
                ['line' => 1, 'pos' => 2],
                ['line' => 2, 'pos' => 2],
                ['line' => 3, 'pos' => 2],
                ['line' => 4, 'pos' => 1]
            ],
            [ // Line 9
                ['line' => 0, 'pos' => 1],
                ['line' => 1, 'pos' => 0],
                ['line' => 2, 'pos' => 0],
                ['line' => 3, 'pos' => 0],
                ['line' => 4, 'pos' => 1]
            ]
        ];

        $ids = [];

        foreach($images as $img) foreach($list as $key => $line) {
            $lineIDs = [];
            if($key < $linesCount) {
                $count = 0;
                $isBrocked = false;
                foreach($line as $l) {
                    if(($lines[$l['line']][$l['pos']]['id'] == $img->id) || ($lines[$l['line']][$l['pos']]['type'] == 1 && $count > 0)) {
                        $count++;
                        $lineIDs[] = [
                            'line' => $l['line'],
                            'pos' => $l['pos']
                        ];
                    } else if($count >= 3) {
                        $found = false;
                        foreach($ids as $id) if($id['line'] == $key+1) $found = true;
                        if(!$found && !$isBrocked) {
                            $ids[] = [
                                'id' => $img->id,
                                'line' => $key+1,
                                'count' => $count
                            ];
                            $lineKeys[$key] = $lineIDs;
                            $counts[] = $count;
                        }
                    } else {
                        $count = 0;
                        $isBrocked = true;
                    }
                }
                if($count >= 3 && !$isBrocked) {
                    $found = false;
                    foreach($ids as $id) if($id['line'] == $key+1) $found = true;
                    if(!$found) {
                        $ids[] = [
                            'id' => $img->id,
                            'line' => $key+1,
                            'count' => $count
                        ];
                        $lineKeys[$key] = $lineIDs;
                        $counts[] = $count;
                    }
                }
            }

        }

        foreach($list as $i => $line) $list[$i] = array_reverse($line);

        foreach($images as $img) foreach($list as $key => $line) {
            if($key < $linesCount) {
                $lineIDs = [];
                $count = 0;
                $isBrocked = false;
                foreach($line as $l) {
                    if(($lines[$l['line']][$l['pos']]['id'] == $img->id) || ($lines[$l['line']][$l['pos']]['type'] == 1 && $count > 0)) {
                        $count++;
                        $lineIDs[] = [
                            'line' => $l['line'],
                            'pos' => $l['pos']
                        ];
                    } else if($count >= 3) {
                        $found = false;
                        foreach($ids as $id) if($id['line'] == $key+1) $found = true;
                        if(!$found && !$isBrocked) {
                            $ids[] = [
                                'id' => $img->id,
                                'line' => $key+1,
                                'count' => $count
                            ];
                            $lineKeys[$key] = $lineIDs;
                            $counts[] = $count;
                        }
                    } else {
                        $count = 0;
                        $isBrocked = true;
                    }
                }
                if($count >= 3 && !$isBrocked) {
                    $found = false;
                    foreach($ids as $id) if($id['line'] == $key+1) $found = true;
                    if(!$found) {
                        $ids[] = [
                            'id' => $img->id,
                            'line' => $key+1,
                            'count' => $count
                        ];
                        $lineKeys[$key] = $lineIDs;
                        $counts[] = $count;
                    }
                }
            }

        }

        foreach($lineKeys as $id => $lineS) if(!is_null($lineS)) foreach($lineS as $l) $lines[$l['line']][$l['pos']]['line_id'][] = $id+1;

        return [
            'lines' => $lines,
            'list' => $ids,
            'keys' => $lineKeys,
            'counts' => $counts
        ];
    }

    public function getMaxBet()
    {
        $line = 9;
        $value = $this->config->bandit_max_bet;
        if($this->config->bandit_max_bet == 0) {
            // $value = floor($this->user->money/$line);
            for($i = 9; $i > 0; $i--) {
                $value = floor($this->user->money/$i);
                if($value >= $this->config->bandit_min_bet) return [
                    'success' => true,
                    'lines' => $i,
                    'bet' => $value
                ];
            }

            return [
                'success' => false,
                'msg' => $this->lang['bandit']['minbet'].$this->config->bandit_min_bet
            ];
        }

        return [
            'success' => false,
            'lines' => $line,
            'bet' => $value
        ];
    }

    private function getFreeSpins()
    {
        $list = [];
        for($i = 0; $i < $this->config->bandit_free_spins_chance; $i++) $list[] = true;
        for($i = 0; $i < (100-$this->config->bandit_free_spins_chance); $i++) $list[] = false;
        shuffle($list);

        $result = $list[mt_rand(0, count($list)-1)];

        if(!$result) return [false, false];
        $list = [3,4,5];
        return [true, mt_rand(0, 2)];
    }

    public function getLines($res, $linesCount)
    {
        // if($isGamed) $res = true;
        $images = DB::table('bandit_images')->where('type', '!=', 2)->get();
        $lines = [];
        $cheked = false;
        while(!$cheked) {
            for($lc = 0; $lc < 5; $lc++) {
                $lines[$lc] = [];
                for($ic = 0; $ic < 3; $ic++) {
                    $itemKey = mt_rand(0, count($images)-1);
                    $lines[$lc][$ic] = [
                        'id' => $images[$itemKey]->id,
                        'url' => $images[$itemKey]->url,
                        'type' => $images[$itemKey]->type
                    ];
                }
            }

            $result = $this->getWinnerLines($lines, $images, $linesCount);
            switch ($res) {
                case true:
                    if(count($result['list']) > 0) $cheked = true;
                    break;
                case false:
                    if(count($result['list']) < 1) $cheked = true;
                    break;
            }
        }




        return $lines;
    }

    // Games

    public function quest(Request $r)
    {
        $game = DB::table('bandit')->where('user_id', $this->user->id)->where('status', 1)->orderBy('id', 'desc')->first();
        if(is_null($game)) return ['success' => false, 'msg' => $this->lang['bandit']['gamend']];
        // if($game->status == 3) return ['success' => false, 'msg' => 'Игра закончилась!'];

        $values = json_decode($game->values);
        $list = [2,3,4,'spins',null,null,null,null,null,null,null,null,null,null,null];

        if(count($values) > 0) {
            foreach($list as $k => $l) foreach($values as $v) if($l == floatval($v)) unset($list[$k]);
            $r = [];
            foreach($list as $l) if(!is_null($l)) $r[] = $l;
            for($i = count($list)-1; $i < 15; $i++) $list[] = null;
        }

        shuffle($list);

        $key = mt_rand(0, 14);



        $value = $game->winner_value;
        $this->user->money -= $value;

        $data = ['url' => null, 'value' => null, 'multiplier' => null];

        $status = 'allowed';

        // $list[$key] = 2;

        switch ($list[$key]) {
            case null:
                DB::table('bandit')->where('user_id', $this->user->id)->update(['status' => 3]);
                $n = mt_rand(1,5);
                $data['url'] = 'http://dotaregal.com/assets/frontend/images/slot/bonus/'.$n.'.png';
                $value = 0;
                $status = 'quit';
                $data['multiplier'] = 0;
                break;
            case 2 :
                $value *= 2;
                $data['url'] = 'http://dotaregal.com/assets/frontend/images/slot/bonus/x2.png';
                $values[] = 2;
                $data['multiplier'] = 2;
                break;
            case 3 :
                $value *= 3;
                $data['url'] = 'http://dotaregal.com/assets/frontend/images/slot/bonus/x3.png';
                $values[] = 3;
                $data['multiplier'] = 3;
                break;
            case 4 :
                $value *= 4;
                $data['url'] = 'http://dotaregal.com/assets/frontend/images/slot/bonus/x4.png';
                $values[] = 4;
                $data['multiplier'] = 4;
                break;
            // case 5 :
            //     $value *= 5;
            //     $data['url'] = 'http://dotaregal.com/assets/frontend/images/slot/bonus/x5.png';
            //     $values[] = 5;
            //     break;
            case 'spins' :
                $data['url'] = 'http://dotaregal.com/assets/frontend/images/slot/free.png';
                $this->user->bandit_spins += 1;
                $values[] = 'spins';
                DB::table('bandit')->where('id', $game->id)->update(['status' => 3]);
                $status = 'spins';
                $data['multiplier'] = null;
                break;
        }

        DB::table('bandit')->where('id', $game->id)->update(['values' => json_encode($values)]);

        $data['value'] = $value;
        if($value > 0) DB::table('bandit')->where('id', $game->id)->update(['winner_value' => $value]);

        $this->user->money += $value;
        PromoController::ref(floor($value), $this->user);
        $this->user->save();

        $data['balance'] = number_format($this->user->money, 0, ' ', ' ');

        if($value > 1000) {
            $value = round($value/1000, 1).'K';
            $data['value'] = $value;
        }

        return [
            'success' => true,
            'data' => $data,
            'status' => $status
        ];
    }

    public function mgame()
    {
        $game = DB::table('bandit')->where('user_id', $this->user->id)->where('status', 1)->orderBy('id', 'desc')->first();
        if(is_null($game)) return ['success' => false, 'msg' => $this->lang['bandit']['gamend']];
        // if($game->status == 3) return ['success' => false, 'msg' => 'Игра закончилась!'];

        $list = DB::table('bandit_images')->where('type', 0)->get();

        $value = $game->winner_value;
        $this->user->money -= $value;

        $item = $list[mt_rand(0, count($list)-1)];

        $mList = json_decode($item->multiplier);
        $multiplier = $mList[2];

        $value *= $multiplier;

        $this->user->money += $value;
        PromoController::ref(floor($value), $this->user);
        $this->user->save();
        if($value > 1000000) $value = round($value/1000000, 1).'M'; else if($value >= 1000) $value = round($value/1000, 1).'K';

        $data = ['url' => $item->url, 'value' => $value, 'balance' => null, 'multiplier' => $multiplier];

        // $this->user->money += $value;
        // $this->user->save();

        $data['balance'] = number_format($this->user->money, 0, ' ', ' ');



        DB::table('bandit')->where('user_id', $this->user->id)->update(['status' => 3]);
        DB::table('bandit')->where('id', $game->id)->update(['winner_value' => $value]);

        return [
            'success' => true,
            'data' => $data
        ];
    }

    public function towers(Request $r)
    {
        $game = DB::table('bandit')->where('user_id', $this->user->id)->where('status', 1)->orderBy('id', 'desc')->first();
        if(is_null($game) || $game->status == 3) return ['success' => false, 'msg' => $this->lang['bandit']['gamend'], 'status' => 'end'];


        $value = $game->winner_value;
        $this->user->money -= $value;

        $tower = mt_rand(1,2);
        if($tower == $r->get('tower')) {
            $value *= 2;
        } else {
            $value = 0;
            DB::table('bandit')->where('user_id', $this->user->id)->update(['status' => 3]);
        }
        if($tower == 1) $rotate = 900; else $rotate = 720;

        $this->user->money += $value;
        PromoController::ref(floor($value), $this->user);
        $this->user->save();

        DB::table('bandit')->where('id', $game->id)->update(['winner_value' => $value]);

        if($value > 1000) $value = round($value/1000).'K';

        return [
            'success' => true,
            'data' => [
                'balance' => number_format($this->user->money, 0, ' ', ' '),
                'rotate' => $rotate,
                'tower' => $tower,
                'win' => $value
            ]
        ];
    }

    public function getOtherLines()
    {
        $images = DB::table('bandit_images')->where('type', 0)->get();
        $list = [];
        foreach($images as $img) $list[] = [
            'url' => $img->url
        ];

        return $list;
    }

    public function profitTest() {
        $images = DB::table('bandit_images')->where('type', '!=', 2)->orderBy('id', 'desc')->get();

        for($linesCount = 1; $linesCount < 10; $linesCount++) {
            #Generate Lines
            $resArray = [];
            $percent = $this->config->bandit_winpercent;
            if($linesCount == 2) $percent = $this->config->bandit_winpercent2;
            if($linesCount == 3) $percent = $this->config->bandit_winpercent3;
            if($linesCount == 4) $percent = $this->config->bandit_winpercent4;
            if($linesCount == 5) $percent = $this->config->bandit_winpercent5;
            if($linesCount == 6) $percent = $this->config->bandit_winpercent6;
            if($linesCount == 7) $percent = $this->config->bandit_winpercent7;
            if($linesCount == 8) $percent = $this->config->bandit_winpercent8;
            if($linesCount == 9) $percent = $this->config->bandit_winpercent9;
            for($i = 0; $i < floor($percent); $i++) $resArray[] = true;
            for($i = 0; $i < floor(100-$percent); $i++) $resArray[] = false;
            shuffle($resArray);

            $avg = 0;
            $min_profit = 100;
            $max_profit = 0;

            for($x = 0; $x < 10; $x++) {
                $sum = 0;
                $def = 0;
                for($i = 0; $i < 100; $i++) {
                    $lines = $this->getLines($resArray[mt_rand(0, count($resArray)-1)], $linesCount);

                    $winners = $this->getWinnerLines($lines, $images, $linesCount);

                    $m = 0;
                    $price = 0;
                    foreach($winners['list'] as $item) {
                        $image = DB::table('bandit_images')->where('id', $item['id'])->first();
                        $multiplier = 0;
                        if(!is_null($image)) {
                            $list = json_decode($image->multiplier);
                            if($linesCount == 2) $list = json_decode($image->multiplier2);
                            if($linesCount == 3) $list = json_decode($image->multiplier3);
                            if($linesCount == 4) $list = json_decode($image->multiplier4);
                            if($linesCount == 5) $list = json_decode($image->multiplier5);
                            if($linesCount == 6) $list = json_decode($image->multiplier6);
                            if($linesCount == 7) $list = json_decode($image->multiplier7);
                            if($linesCount == 8) $list = json_decode($image->multiplier8);
                            if($linesCount == 9) $list = json_decode($image->multiplier9);
                            switch ($item['count']) {
                                case 3:
                                    $multiplier = $list[0];
                                    break;
                                case 4:
                                    $multiplier = $list[1];
                                    break;
                                case 5:
                                    $multiplier = $list[2];
                                    break;
                            }
                        }

                        $sum += 100*$multiplier;
                    }
                    // $sum += ((100*$linesCount)*$m);
                    $def += 100*$linesCount;
                }
                $profit = 100-round(($sum/$def)*100, 2);
                $avg += $profit;
                if($profit < $min_profit) $min_profit = $profit;
                if($profit > $max_profit) $max_profit = $profit;
                // echo $def.' / '.$sum.'<br>';
            }

            echo $linesCount.') MIN : '.$min_profit.'%  MAX : '.$max_profit.'%  AVG : '.round($avg/10, 2).'%<br>';
        }
    }

    public function finishGame()
    {
        DB::table('bandit')->where('user_id', $this->user->id)->where('status', 1)->update([
            'bonus_win' => 1,
            'status' => 3
        ]);
        AchievementController::checkAchievement($this->user, $this->redis, $this->lang['achievement_unlock']);
    }

    public function test($user_id)
    {
        $list = DB::table('bandit')->where('user_id', $user_id)->get();
        
        echo 'BANDIT <br>';
        
        $val = 0;
        $pri = 0;
        foreach($list as $game)
        {
            $game->max_count = json_decode($game->max_count);
            
            $icons = '(';
            foreach($game->max_count as $icon) $icons .= $icon.',';
            $icons .= ')';
            
            $bonus = '';
            
            $val += $game->value;
            $pri += $game->winner_value;
            
            if($game->bonus == 1)
            {
                if($game->bonus_win == 0) $bonus = 'Проиграл бонусную игру.';
                else $bonus = 'Выиграл бонусную игру.';
            }
            
            echo 'Поставил '.number_format($game->value, 0, '.', '.').' на '.$game->lines.' линий, из которых '.$game->wins_lines.' выиграли. Множитель : x'.$game->multiplier.' Иконок в ряд : '.$icons.'. '.$bonus.' Получил '.number_format($game->winner_value, 0, '.', '.').' Дата : '.$game->created_at.'<br>';
        }
        
        echo $val.'/'.$pri;
        
        echo '<br> DOUBLE <br>';
        
        
        $list = DB::table('double_bets')->where('user_id', $user_id)->where('is_fake', 0)->get();
        $val = 0;
        $pri = 0;
        foreach($list as $game)
        {
            $val += $game->value;
            $pri += $game->winner_value;
            echo 'Поставил '.number_format($game->value, 0, '.', '.').' на '.$game->type.'. Игра : '.$game->game_id.' Выиграл '.number_format($game->winner_value, 0, '.', '.').' Дата : '.$game->created_at.'<br>';
        }
        echo $val.'/'.$pri;
        
        echo '<br> POKER <br>';
        
        $list = DB::table('poker')->where('user_id', $user_id)->get();
        $val = 0;
        $pri = 0;
        foreach($list as $game)
        {
            $val += $game->value;
            $pri += $game->winner_value;
            echo 'Поставил '.number_format($game->value, 0, '.', '.').'. Выиграл '.number_format($game->winner_value, 0, '.', '.').'  Дата : '.$game->created_at.'<br>';
        }
        echo $val.'/'.$pri;
        echo '<br><br><br> TEST <br>';
        
        $list = DB::table('poker')->where('value', '<', 0)->get();
        echo count($list);
    }
}
