<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use App\Poker;
use App\Double;
use App\Chat;
use App\Promo;
use Carbon\Carbon;
use Session;
use Redis;

class AchievementController extends Controller
{
	public function __construct()
	{
        $this->lang = Parent::getLang();
        $this->redis = Redis::connection();
    }

	public static function checkAchievement($user, $redis, $unlock_message)
	{
		$list = json_decode($user->achievements, true);
		$count = 0;
		foreach($list as $key => $achievement)
		{
			$money_before = $user->money;
			if($achievement['unlock'] == 0) 
			{
                $type = 0;
                $spins = 0;
				switch ($achievement['name'])
				{
					case 'Welcome':
						$list[$key]['unlock'] = 1;
						$user->money += 100;
						PromoController::ref(100, $user);	
						break;
					case 'Beginner':
						$count = $user->roulette+$user->poker+$user->slot_machine;
						if($count >= 100) 
						{
							$list[$key]['unlock'] = 1;
							$user->money += 1000;	
							PromoController::ref(1000, $user);	
						}
						break;
					case 'Playa':
						$count = $user->roulette+$user->poker+$user->slot_machine;
						if($count >= 1000) 
						{
							$list[$key]['unlock'] = 1;
							$user->money += 10000;
							PromoController::ref(10000, $user);	
						}
						break;
					case 'Investor':
						$sum = DB::table('deposits')->where('user_id', $user->steamid64)->where('status', 3)->sum('price');
						if($sum >= 100000)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 10000;
							PromoController::ref(10000, $user);
						}
						break;
					case 'Spam':
						$message = Chat::where('user_id', $user->id)->first();
						if(!is_null($message))
						{
							$list[$key]['unlock'] = 1;
							$user->money += 100;
							PromoController::ref(100, $user);
						}
						break;
					case 'Rebel':
						if($user->is_banned == 1)
						{
							$list[$key]['unlock'] = 1;
						}
						break;
					case 'Start-up':
						$deposit = DB::table('deposits')->where('user_id', $user->steamid64)->where('status', 3)->first();
						if(!is_null($deposit))
						{
							$list[$key]['unlock'] = 1;
							$user->money += 100;
							PromoController::ref(100, $user);
						}
						break;
					case 'Rookie':
						if($user->lvl >= 5)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 500;
							PromoController::ref(500, $user);
						}
						break;
					case 'Legend':
						if($user->lvl >= 25)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 25000;
							PromoController::ref(25000, $user);
						}
						break;
					case 'Greedy':
						$count = DB::table('promo_list')->where('user_id', $user->id)->count();
						if($count >= 5)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 100;
							PromoController::ref(100, $user);	
						}
						break;
					case 'Dota Regal':
						if($user->lvl >= 30)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 10000;
							PromoController::ref(10000, $user);
						}
						break;
					case 'Regal Bank':
						$sum = DB::table('deposits')->where('user_id', $user->steamid64)->where('status', 3)->sum('price');
						if($sum >= 1000000)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 100000;
							PromoController::ref(100000, $user);
						}
						break;
					case 'Collector':
						$achievements = json_decode($user->achievements, true);
						$count = 0;
						foreach($achievements as $a => $un) if($un['unlock']) $count++;
						if($count >= 10)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 1000;
							PromoController::ref(1000, $user);	
						}
						break;
					case 'Dead dream':
						$achievements = json_decode($user->achievements, true);
						$count = 0;
						foreach($achievements as $a => $un) if($un['unlock']) $count++;
						if($count+1 >= count($achievements))
						{
							$list[$key]['unlock'] = 1;
							$user->money += 100000;	
							PromoController::ref(100000, $user);
						}
						break;
					case 'Elite':
						if($user->lvl >= 15)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 5000;
							PromoController::ref(5000, $user);
						}
						break;
					case 'Royal':
						if($user->lvl >= 20)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 10000;
							PromoController::ref(10000, $user);
						}
						break;
					case 'Keep her steady':
						if($user->lvl >= 10)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 1000;
							PromoController::ref(1000, $user);
						}
						break;
					case 'Shopper':
						$owner = DB::table('bots')->where('type', 1)->first();
						if(!is_null($owner)) 
						{
							$withdraw = DB::table('withdraws')->where('user_id', $user->id)->where('status', 4)->first();
							if(!is_null($withdraw))
							{
								$list[$key]['unlock'] = 1;
								$user->money += 500;
								PromoController::ref(500, $user);
							}
						}						
						break;
					case 'so, what is next?':
						if($user->lvl >= 99)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 1000000;
							PromoController::ref(1000000, $user);
						}
						break;
					case 'Are you okay?':
						if($user->lvl >= 50)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 250000;
							PromoController::ref(250000, $user);
						}
						break;
					case 'Tripple boy (silver)':
						$count = Poker::where('user_id', $user->id)->where('combo', 4)->where('is_win', 1)->count();
						if($count >= 20)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 3000;
							PromoController::ref(3000, $user);
						}
						break;
					case 'Tripple boy (gold)':
						$count = Poker::where('user_id', $user->id)->where('combo', 4)->where('is_win', 1)->count();
						if($count >= 100)
						{
							$winner_value = Poker::where('user_id', $user->id)->where('combo', 4)->sum('winner_value');
							$list[$key]['unlock'] = 1;
							$user->money += $winner_value*3;
							PromoController::ref(floor($winner_value*3), $user);
						}
						break;
					case 'Cardsharper (silver)':
						$count = Poker::where('user_id', $user->id)->where('combo', 10)->count();
						if($count >= 1)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 5000;
							PromoController::ref(5000, $user);
						}
						break;
					case 'Cardsharper (gold)':
						$count = Poker::where('user_id', $user->id)->where('is_win', 1)->where('combo', 10)->count();
						if($count >= 10)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 25000;
							PromoController::ref(25000, $user);
						}
						break;
					case 'Lol':
							$is_unlocked = false;
							$double = DB::table('double_bets')->select(DB::raw('SUM(value) as value'))->where('user_id', $user->id)->where('is_win', 0)->where('is_fake', 0)->groupBy('game_id')->get();
							foreach($double as $v) if($v->value >= 100000) {
								$list[$key]['unlock'] = 1;
								$user->money += 50000;
								$is_unlocked = true;
								PromoController::ref(50000, $user);
							}

							$poker = Poker::where('user_id', $user->id)->where('value', '>=', 100000)->where('winner_value', 0)->first();
							if(!$is_unlocked && !is_null($poker))
							{
								$list[$key]['unlock'] = 1;
								$user->money += 50000;
								$is_unlocked = true;
								PromoController::ref(50000, $user);
							}

							$bandit = DB::table('bandit')->where('user_id', $user->id)->where('value', '>=', 100000)->where('winner_value', 0)->first();
							if(!$is_unlocked && !is_null($bandit))
							{
								$list[$key]['unlock'] = 1;
								$user->money += 50000;
								$is_unlocked = true;
								PromoController::ref(50000, $user);
							}
						break;
					case 'First blood':
							$games = [];
							$double = DB::table('double_bets')->where('user_id', $user->id)->where('is_win', 1)->where('is_fake', 0)->orderBy('id', 'desc')->first();
							$poker = Poker::where('user_id', $user->id)->where('is_win', 1)->orderBy('id', 'desc')->first();
							$bandit = DB::table('bandit')->where('user_id', $user->id)->where('winner_value', '>', 0)->orderBy('id', 'desc')->first();

							if(!is_null($double)) $games[] = [
								'value' => $double->winner_value,
								'date' => strtotime($double->created_at)
							];
							if(!is_null($poker)) $games[] = [
								'value' => $poker->winner_value,
								'date' => strtotime($poker->created_at)
							];
							if(!is_null($bandit)) $games[] = [
								'value' => $bandit->winner_value,
								'date' => strtotime($bandit->created_at)
							];

							if(count($games) > 0)
							{
								usort($games, function($a, $b) {
									return($a['date']-$b['date']);
								});

								$user->money += $games[0]['value']*2;
								PromoController::ref(floor($games[0]['value']*2), $user);

								$list[$key]['unlock'] = 1;
							}
						break;
					case 'Like a cat':
						$count = DB::table('bandit')->where('user_id', $user->id)->where('lines', 9)->where('winner_value', 0)->count();
						if($count >= 9)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 1800;
							PromoController::ref(1800, $user);
						}
						break;
					case 'Practice':
						$double = DB::table('double_bets')->where('user_id', $user->id)->where('is_fake', 0)->first();
						$poker = Poker::where('user_id', $user->id)->first();
						$bandit = DB::table('bandit')->where('user_id', $user->id)->first();
						// return json_encode(!is_null($double) && !is_null($poker) && !is_null($bandit));
						if(!is_null($double) && !is_null($poker) && !is_null($bandit))
						{
							$list[$key]['unlock'] = 1;
							$user->money += 300;
							PromoController::ref(300, $user);	
						}
						break;
					case 'Freebie':
						if($user->bandit_spins >= 100)
						{
							$list[$key]['unlock'] = 1;
							$user->bandit_spins += 10;
                            $type = 1;
                            $spins = 10;
						}
						break;
					case 'Traffic light':
						$red_count = DB::table('double_bets')->where('user_id', $user->id)->where('is_fake', 0)->where('type', 'red')->where('is_win', 1)->count();
						$yellow_count = DB::table('double_bets')->where('user_id', $user->id)->where('is_fake', 0)->where('type', 'yellow')->where('is_win', 1)->count();
						$green_count = DB::table('double_bets')->where('user_id', $user->id)->where('is_fake', 0)->where('type', 'green')->where('is_win', 1)->count();
						if($red_count >= 125 && $yellow_count >= 125 && $green_count >= 125)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 5000;
							PromoController::ref(5000, $user);	
						}
						break;
					case 'Never give up':
						$poker = Poker::where('user_id', $user->id)->where('combo', 1)->where('is_win', 1)->first();
						if(!is_null($poker))
						{
							$list[$key]['unlock'] = 1;
							$user->money += 1000;
							PromoController::ref(1000, $user);	
						}
						break;
					case 'Guess my pair':
						$count = Poker::where('user_id', $user->id)->where('is_win', 1)->where('combo', 2)->where('summ', 6)->count();
						if($count >= 5)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 3000;
							PromoController::ref(3000, $user);	
						}
						break;
					case '666':
						$count = DB::table('bandit')->where('user_id', $user->id)->where('lines', 6)->count();
						if($count >= 666)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 6666;	
							PromoController::ref(6666, $user);	
						}
						break;
					case 'Baker’s dozen':
						$count = DB::table('double_bets')->where('user_id', $user->id)->where('is_fake', 0)->where('type', 'red')->where('is_win', 1)->count();
						if($count >= 13)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 1300;	
							PromoController::ref(1300, $user);		
						}
						break;
					case 'Straight on my street':
						$poker = Poker::where('user_id', $user->id)->where('combo', 5)->first();
						if(!is_null($poker))
						{
							$list[$key]['unlock'] = 1;
							$user->money += 1000;	
							PromoController::ref(1000, $user);		
						}
						break;
					case 'Four kingdoms':
						$poker = Poker::where('user_id', $user->id)->where('combo', 8)->where('summ', 52)->first();
						if(!is_null($poker))
						{
							$list[$key]['unlock'] = 1;
							$user->money += 4000;
							PromoController::ref(4000, $user);			
						}
						break;
					case 'Highway to hell':
						$games = DB::table('double_bets')->where('user_id', $user->id)->where('is_win', 0)->where('is_fake', 0)->groupBy('game_id')->orderBy('id', 'asc')->get();
						$count = 1;
						$doIt = true;

						$lastID = 0;
						foreach ($games as $bet) {
							if($doIt)
							{
								if($bet->game_id == $lastID+1) $count++; else $count = 0;
								if(($count+1) == 10)
								{
									$doIt = false;
									$list[$key]['unlock'] = 1;
									$user->money += 1000;
									PromoController::ref(1000, $user);	
								}
							}
							$lastID = $bet->game_id;
						}
						break;
					case 'Lucky seven':
						$games = DB::table('double_bets')->where('user_id', $user->id)->where('is_win', 1)->where('is_fake', 0)->groupBy('game_id')->orderBy('id', 'asc')->get();
						$count = 0;
						$doIt = true;

						$lastID = 0;
						foreach ($games as $bet) {
							if($doIt)
							{
								if($bet->game_id == $lastID+1) $count++; else $count = 0;
								if($count == 7)
								{
									$doIt = false;
									$list[$key]['unlock'] = 1;
									$user->money += 1000;
									PromoController::ref(1000, $user);	
								}
							}
							$lastID = $bet->game_id;
						}
						break;
					case 'Who is your daddy?':
						$bet = DB::table('double_bets')->where('user_id', $user->id)->where('is_fake', 0)->where('value', 10000)->where('is_win', 1)->first();
						if(!is_null($bet))
						{
							$list[$key]['unlock'] = 1;
							$user->money += 10000;	
							PromoController::ref(10000, $user);	
						}
						break;
					case 'Red devil':
						$games = DB::table('double_bets')->where('user_id', $user->id)->where('is_win', 1)->where('is_fake', 0)->where('type', 'red')->groupBy('game_id')->orderBy('id', 'asc')->get();
						$count = 0;
						$doIt = true;
						$lastID = 0;
						foreach ($games as $bet) {
							if($doIt)
							{
								if($bet->game_id == $lastID+1) $count++; else $count = 0;
								if(($count+1) == 3)
								{
									$doIt = false;
									$list[$key]['unlock'] = 1;
									$user->money += floor($bet->value*3);
									PromoController::ref(floor($bet->value*3), $user);
								}
							}
							$lastID = $bet->game_id;
						}
						break;
					case 'Millionaire':
						// $poker = Poker::where('user_id', $user->id)->where('value', 1000000)->first();
						$double = DB::table('double_bets')->where('user_id', $user->id)->where('is_fake', 0)->where('value', 1000000)->first();
						// $bandit = DB::table('bandit')->where('user_id', $user->id)->where('value', 1000000)->first();
						if(!is_null($double))
						{
							$list[$key]['unlock'] = 1;
							$user->money += 100000;
							PromoController::ref(100000, $user);
						}
						break;
					case 'Nine Line':
						$bet = DB::table('bandit')->where('user_id', $user->id)->where('lines', 9)->where('winner_value', '>', 0)->first();
						if(!is_null($bet))
						{
							$list[$key]['unlock'] = 1;
							$user->money += 900;
							PromoController::ref(900, $user);
						}
						break;
					case 'School diary':
						$poker = Poker::where('user_id', $user->id)->where('combo', 8)->where('summ', 8)->where('is_win', 1)->first();
						if(!is_null($poker))
						{
							$list[$key]['unlock'] = 1;
							$user->money += 2000;
							PromoController::ref(2000, $user);		
						}
						break;
					case 'Roofless':
						$poker = Poker::where('user_id', $user->id)->first();
						$double = DB::table('double_bets')->where('user_id', $user->id)->where('is_fake', 0)->first();
						$bandit = DB::table('bandit')->where('user_id', $user->id)->first();
						if(!is_null($poker) || !is_null($double) || !is_null($bandit))
						{
							if($user->money == 0)
							{
								$list[$key]['unlock'] = 1;
								$user->money += 100;
								PromoController::ref(100, $user);
							}
						}
						break;
					case 'Always in the game':
						$poker = Poker::where('user_id', $user->id)->where('is_folded', 0)->orderBy('id', 'asc')->get();
						$count = 0;
						$doIt = true;
						$lastID = 0;
						foreach ($poker as $game) {
							if($doIt)
							{
								if($game->id == $lastID+1) $count++; else $count = 0;
								if($count == 15)
								{
									$doIt = false;
									$list[$key]['unlock'] = 1;
									$user->money += 1000;
									PromoController::ref(1000, $user);
								}
							}
							$lastID = $game->id;
						}
						break;
					case 'Psycho':
						$count = DB::table('double_bets')->where('user_id', $user->id)->where('is_fake', 0)->where('created_at', '>', Carbon::today()->format('Y-m-d 00:00:00'))->groupBy('game_id')->get();
						if(count($count) >= 500)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 5000;	
							PromoController::ref(5000, $user);		
						}
						break;
					case 'Bad achievement':
						$count = DB::table('double_bets')->where('user_id', $user->id)->where('is_fake', 0)->where('is_win', 0)->count();
						if($count >= 1000)
						{
							$list[$key]['unlock'] = 1;
							$user->money += 1000;
							PromoController::ref(1000, $user);		
						}
						break;
					case 'Fortune (silver)':
						$game = DB::table('bandit')->where('user_id', $user->id)->where('bonus', 1)->first();
						if(!is_null($game))
						{
							$list[$key]['unlock'] = 1;
							$user->bandit_spins += 1;	
                            $type = 1;
                            $spins = 1;
						}
						break;
					case 'Fortune (gold)':
						$count = DB::table('bandit')->where('user_id', $user->id)->where('bonus', 1)->where('bonus_win', 1)->count();
						if($count >= 10)
						{
							$list[$key]['unlock'] = 1;
							$user->bandit_spins += 10;	
                            $type = 1;
                            $spins = 10;
						}
						break;
					case 'Bandit':
						$game = DB::table('bandit')->where('user_id', $user->id)->where('lines', 9)->where('wins_lines', 9)->first();
						if(!is_null($game))
						{
							$list[$key]['unlock'] = 1;
							$user->bandit_spins += 10;	
                            $type = 1;
                            $spins = 10;
						}
						break;
					case '3x5':
						$game = DB::table('bandit')->where('user_id', $user->id)->where('lines', 3)->where('wins_lines', 3)->get();
						$doIt = true;
						foreach($game as $g)
						{
							$c = 0;
							$counts = json_decode($g->max_count);
							foreach($counts as $count) if($count == 5) $c++;
							if($c == 3 && $doIt)
							{
								$doIt = false;
								$list[$key]['unlock'] = 1;
								$user->money += 3000;	
								PromoController::ref(3000, $user);
							}
						}
						break;
					case 'Junk':
						$wList = DB::table('withdraws')->where('user_id', $user->id)->where('status', 4)->get();
						$count = 0;
						foreach($wList as $w)
						{
							$items = json_decode($w->items);
							foreach($items as $item) if($item->price < 600) $count++;
						}
						
						if($count >= 100)
						{
							$user->money += 6000;
							$list[$key]['unlock'] = 1;
							PromoController::ref(6000, $user);
						}
						break;
					case 'Autopilot':
						$count = DB::table('bandit')->where('user_id', $user->id)->where('is_auto', 1)->count();
						if($count >= 100)
						{
							$user->money += 1000;
							$list[$key]['unlock'] = 1;
							PromoController::ref(1000, $user);
						}
						break;
					case 'Desperate':
						$dep = DB::table('deposits')->where('user_id', $user->id)->where('status', 3)->get();
						$doIt = true;
						foreach($dep as $d)
						{
							$items = json_decode($d->items);
							foreach($items as $item)
							{
								if($doIt && $item->rarity == 'Arcana')
								{
									$doIt = false;
									$user->money += 1000;
									$list[$key]['unlock'] = 1;
									PromoController::ref(1000, $user);				
								}
							}
						}
						break;
					case 'Headstrong':
						$dates = [];
						$dates[] = Carbon::today();
						for($i = 0; $i < 9; $i++)
						{
							$dates[] = Carbon::parse($dates[count($dates)-1])->addHours(-24);
						}

						$bets = [];
						foreach($dates as $date)
						{
							$counts = DB::table('double_bets')->where('user_id', $user->id)->where('is_fake', 0)->where('created_at', '>', $date)->where('created_at', '<', Carbon::parse($date)->addHours(24))->count();
							if($count == 0) $count = DB::table('bandit')->where('user_id', $user->id)->where('created_at', '>', $date)->where('created_at', '<', Carbon::parse($date)->addHours(24))->count();
							if($count == 0) $count = Poker::where('user_id', $user->id)->where('created_at', '>', $date)->where('created_at', '<', Carbon::parse($date)->addHours(24))->count();
							$bets[] = [
								'date' => $date,
								'count' => $counts
							];
						}

						$c = 0;
						foreach($bets as $bet) if($bet['count'] > 0) $c++; else $c = 0;

						if($c == count($bets))
						{
							$user->money += 1000;
							$list[$key]['unlock'] = 1;	
							PromoController::ref(1000, $user);				
						};
						break;
					case 'Support':
						$proceed = DB::table('support')->where('user_id', $user->id)->first();
						if(!is_null($proceed))
						{
							$user->money += 100;
							$list[$key]['unlock'] = 1;
							PromoController::ref(100, $user);
						}
						break;
					case 'Squad':
						$count = User::where('my_ref', $user->ref)->count();
						if($count >= 10)
						{
							$user->money += 1000;
							$list[$key]['unlock'] = 1;
							PromoController::ref(1000, $user);
						}
						break;
					case 'Flash':
						if($user->today_lvls >= 10)
						{
							$user->money += 2000;
							$list[$key]['unlock'] = 1;
							PromoController::ref(2000, $user);
						}
						break;
					case 'All in':
						$bet = DB::table('double_bets')->where('user_id', $user->id)->where('is_fake', 0)->where('balance', 0)->first();
						if(!is_null($bet))
						{
							$user->money += 500;
							$list[$key]['unlock'] = 1;
							PromoController::ref(500, $user);
						}
						break;

				}
				if($list[$key]['unlock'] == 1)
				{
                    if($type == 0)
                    {
                        $redis->publish('message', json_encode([
                            'user_id' => $user->id,
                            'msg' => $unlock_message.$achievement['name'].' (+'.($user->money-$money_before).')',
                            'type' => 'info'
                        ]));
                    } else {
                        $redis->publish('message', json_encode([
                            'user_id' => $user->id,
                            'msg' => $unlock_message.$achievement['name'].' (+'.$spins.' free spin)',
                            'type' => 'info'
                        ]));   
                    }
				}
			}
		}

		// if($count > 0)
		// {
		// }

        $redis->publish('updateBalance', json_encode([
            'user_id' => $user->id,
            'balance' => number_format($user->money, 0, ' ', ' ')
        ]));
		$user->achievements = json_encode($list);
		$user->save();
	}

	public function injectAchievements()
	{
		$users = User::get();
		// foreach($users as $user) $this->insert($user->steamid64);
		// return 'success';
		return $this->checkAchievement($users[0]);
	}

	public static function insert()
	{
		$list = [  
		    [  
		        "name"=>"Welcome",
		        "unlock"=> 0,
		        "category" => "lvl",
		        "img" => 1
		    ],
		    [  
		        "name"=>"Rookie",
		        "unlock"=> 0,
		        "category" => "lvl",
		        "img" => 2
		    ],
		    [  
		        "name"=>"Keep her steady",
		        "unlock"=> 0,
		        "category" => "lvl",
		        "img" => 3
		    ],
		    [  
		        "name"=>"Elite",
		        "unlock"=> 0,
		        "category" => "lvl",
		        "img" => 4
		    ],
		    [  
		        "name"=>"Royal",
		        "unlock"=> 0,
		        "category" => "lvl",
		        "img" => 5
		    ],
		    [  
		        "name"=>"Legend",
		        "unlock"=> 0,
		        "category" => "lvl",
		        "img" => 6
		    ],
		    [  
		        "name"=>"Dota Regal",
		        "unlock"=> 0,
		        "category" => "lvl",
		        "img" => 7
		    ],
		    [  
		        "name"=>"Are you okay?",
		        "unlock"=> 0,
		        "category" => "lvl",
		        "img" => 8
		    ],
		    [  
		        "name"=>"so, what is next?",
		        "unlock"=> 0,
		        "category" => "lvl",
		        "img" => 9
		    ],


		    /* ___________________________________ */


		    [  
		        "name"=>"Regal Bank",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 10
		    ],
		    [  
		        "name"=>"Flash",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 11
		    ],
		    [  
		        "name"=>"Collector",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 12
		    ],
		    [  
		        "name"=>"Dead dream",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 13
		    ],
		    [  
		        "name"=>"First blood",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 14
		    ],
		    [  
		        "name"=>"Beginner",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 15
		    ],
		    [  
		        "name"=>"Playa",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 16
		    ],
		    [  
		        "name"=>"Investor",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 17
		    ],
		    [  
		        "name"=>"Squad",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 18
		    ],
		    [  
		        "name"=>"Headstrong",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 19
		    ],
		    [  
		        "name"=>"Lol",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 20
		    ],
		    [  
		        "name"=>"Spam",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 21
		    ],
		    [  
		        "name"=>"Desperate",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 22
		    ],
		    [  
		        "name"=>"Rebel",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 23
		    ],
		    [  
		        "name"=>"Start-up",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 24
		    ],
		    [  
		        "name"=>"Roofless",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 25
		    ],
		    [  
		        "name"=>"Greedy",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 26
		    ],
		    [  
		        "name"=>"Shopper",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 27
		    ],
		    [  
		        "name"=>"Practice",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 28
		    ],
		    [  
		        "name"=>"Support",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 29
		    ],
		    [  
		        "name"=>"Junk",
		        "unlock"=> 0,
		        "category" => "other",
		        "img" => 30
		    ],


		    /* ___________________________________ */


		    [  
		        "name"=>"Tripple boy (silver)",
		        "unlock"=> 0,
		        "category" => "poker",
		        "img" => 51
		    ],
		    [  
		        "name"=>"Tripple boy (gold)",
		        "unlock"=> 0,
		        "category" => "poker",
		        "img" => 52
		    ],
		    [  
		        "name"=>"Cardsharper (silver)",
		        "unlock"=> 0,
		        "category" => "poker",
		        "img" => 53
		    ],
		    [  
		        "name"=>"Cardsharper (gold)",
		        "unlock"=> 0,
		        "category" => "poker",
		        "img" => 54
		    ],
		    [  
		        "name"=>"Straight on my street",
		        "unlock"=> 0,
		        "category" => "poker",
		        "img" => 56
		    ],
		    [  
		        "name"=>"Always in the game",
		        "unlock"=> 0,
		        "category" => "poker",
		        "img" => 57
		    ],
		    [  
		        "name"=>"School diary",
		        "unlock"=> 0,
		        "category" => "poker",
		        "img" => 55
		    ],
		    [  
		        "name"=>"Four kingdoms",
		        "unlock"=> 0,
		        "category" => "poker",
		        "img" => 58
		    ],
		    [  
		        "name"=>"Guess my pair",
		        "unlock"=> 0,
		        "category" => "poker",
		        "img" => 59
		    ],
		    [  
		        "name"=>"Never give up",
		        "unlock"=> 0,
		        "category" => "poker",
		        "img" => 60
		    ],


		    /* ___________________________________ */


		    [  
		        "name"=>"3x5",
		        "unlock"=> 0,
		        "category" => "bandit",
		        "img" => 46
		    ],
		    [  
		        "name"=>"666",
		        "unlock"=> 0,
		        "category" => "bandit",
		        "img" => 47
		    ],
		    [  
		        "name"=>"Like a cat",
		        "unlock"=> 0,
		        "category" => "bandit",
		        "img" => 48
		    ],
		    [  
		        "name"=>"Autopilot",
		        "unlock"=> 0,
		        "category" => "bandit",
		        "img" => 49
		    ],
		    [  
		        "name"=>"Freebie",
		        "unlock"=> 0,
		        "category" => "bandit",
		        "img" => 50
			],
		    [  
		        "name"=>"Bandit",
		        "unlock"=> 0,
		        "category" => "bandit",
		        "img" => 44
		    ],
		    [  
		        "name"=>"Fortune (silver)",
		        "unlock"=> 0,
		        "category" => "bandit",
		        "img" => 42
		    ],
		    [  
		        "name"=>"Fortune (gold)",
		        "unlock"=> 0,
		        "category" => "bandit",
		        "img" => 43
		    ],
		    [  
		        "name"=>"Nine Line",
		        "unlock"=> 0,
		        "category" => "bandit",
		        "img" => 41
		    ],
		    [  
		        "name"=>"Thug life",
		        "unlock"=> 0,
		        "category" => "bandit",
		        "img" => 45
		    ],


		    /* ___________________________________ */


		    [  
		        "name"=>"Highway to hell",
		        "unlock"=> 0,
		        "category" => "double",
		        "img" => 31
		    ],
		    [  
		        "name"=>"Lucky seven",
		        "unlock"=> 0,
		        "category" => "double",
		        "img" => 32
		    ],
		    [  
		        "name"=>"Who is your daddy?",
		        "unlock"=> 0,
		        "category" => "double",
		        "img" => 33
		    ],
		    [  
		        "name"=>"Red devil",
		        "unlock"=> 0,
		        "category" => "double",
		        "img" => 34
		    ],
		    [  
		        "name"=>"Millionaire",
		        "unlock"=> 0,
		        "category" => "double",
		        "img" => 35
		    ],
		    [  
		        "name"=>"Psycho",
		        "unlock"=> 0,
		        "category" => "double",
		        "img" => 36
		    ],
		    [  
		        "name"=>"Baker’s dozen",
		        "unlock"=> 0,
		        "category" => "double",
		        "img" => 37
		    ],
		    [  
		        "name"=>"Bad achievement",
		        "unlock"=> 0,
		        "category" => "double",
		        "img" => 38
		    ],
		    [  
		        "name"=>"Traffic light",
		        "unlock"=> 0,
		        "category" => "double",
		        "img" => 39
		    ],
		    [
		    	"name" => 'All in',
		    	"unlock" => 0,
		    	"category" => 'double',
		        "img" => 40
		    ]
		];

		return json_encode($list);

		// // User::where('steamid64', $steamid64)->update(['achievements' => json_encode($list)]);
		// DB::table('users')->update(['achievements' => json_encode($list)]);
	}

	public function test()
	{
		$user = User::where('id', 3865)->first();
		return $this->checkAchievement($user, $this->redis, 'Achievement unlock : ');
		// return count(json_decode($user->achievements));
		
	}
}