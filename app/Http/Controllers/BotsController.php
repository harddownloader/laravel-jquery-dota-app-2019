<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use Redis;
use Carbon\Carbon;
use Auth;
use Illuminate\Http\Request;
use App\Items;
use App\Settings;
use App\Shop;

class BotsController extends Controller
{
    public function __construct()
    {
        $this->redis = Redis::connection();
        if(Auth::check())
        {
            $this->user = Auth::user();
            view()->share('u', $this->user);
        }
        $this->config = Settings::first();
        view()->share('config', $this->config);
        $this->prices = json_decode(\Storage::disk('public')->get('file.txt'), true);

        $this->lang = Parent::getLang();
        view()->share('lang', $this->lang);
    }

    /*
     * DEPOSIT
     */
    public function deposit()
    {
        parent::setTitle($this->lang['shop']['dep_title']);
        return view('pages.deposit');
    }

    public function deposit_parse()
    {
        if(Auth::guest()) return [
            'success' => false,
            'msg' => $this->lang['shop']['must_auth']
        ];

        $list = $this->parseItems($this->user->steamid64, true);
        if(!$list['success']) return $list;
        $items = [];
        foreach($list['items'] as $key => $item) {
            if($item['price'] >= $this->config->dep_minprice_item) $list['items'][$key]['is_active'] = 1; else $list['items'][$key]['is_active'] = 0;
            $items[] = $list['items'][$key];
        }

        usort($items, function($a, $b) {
            return($b['price']-$a['price']);
        });

        $aspects = [];
        if(count($items) > 0) $aspects = $this->getAspects($items);

        return [
            'success' => true,
            'items' => $items,
            'aspects' => $aspects
        ];
    }

    public function deposit_send(Request $r)
    {
//        if($this->user->permission != 2)
//        {
//            return [
//                'success' => false,
//                'msg' => 'Deposits will be available after 00.00.'
//            ];
//        }

        if(is_null($this->user->trade)) return [
            'success' => false,
            'msg' => $this->lang['shop']['trade_link']
        ];

        // Проверяем вещи на наличие и готовим их к отправке
        $sendItems = [];
        $items = [];
        $myItems = $this->parseItems($this->user->steamid64, false);

        if($myItems['success']) $myItems = $myItems['items']; else return $myItems;

        $returnPrice = 0;
        $returnValue = 0;

        foreach($r->get('items') as $item)
        {
            $found = false;

            foreach($myItems as $i)
            {
                if($i['classid'] == $item['classid'])
                {
                    $found = true;
                    $items[] = [
//                        'icon_url' => $i['icon_url'],
                        'market_hash_name' => $i['market_hash_name'],
                        'rarity' => $i['rarity'],
                        'price' => $i['price']
                    ];
                    $sendItems[] = [
                        'appid' => 570,
                        'assetid' => $i['assetid'],
                        'contextid' => 2
                    ];

                    $returnPrice += $i['price'];
                    $returnValue += ($i['price']/1000);
                }
            }

            if(!$found) return [
                'success' => false,
                'msg' => 'Could not find one or more items in your inventory.'
            ];
        }

        $bot = DB::table('bots')->inRandomOrder()->first();

        // Добавляем депозит в базу данных со статусом 0
        DB::table('deposits')->insert([
            'offer_id' => null,
            'user_id' => $this->user->id,
            'bot_id' => $bot->id,
            'items' => json_encode($items),
            'sendItems' => json_encode($sendItems),
            'price' => $returnPrice,
            'value' => $returnValue
        ]);

        // Подаем сигнал одному из ботов
        if(!is_null($bot))
        {
            $this->redis->publish('deposit', json_encode([
                'id' => $bot->id
            ]));
        }

        return [
            'success' => true,
            'msg' => 'Your deposit was sent to the queue!'
        ];
    }

    /*
     * SHOP
     */

    public function shop()
    {
        parent::setTitle($this->lang['shop']['with_title']);
        return view('pages.shop');
    }

    public function shop_parse()
    {
        if($this->user->permission == 2) return $this->admin_parse();

        $items = [];

        $list = Shop::get();
        foreach($list as $key => $item) {
            $list[$key]['price'] = (($list[$key]['price']/100)*(100+$this->config->with_percent));
            if($list[$key]['price'] >= $this->config->with_minprice_item) $items[] = $list[$key];
        }
        $aspects = $this->getAspects($items);

        return [
            'success' => true,
            'items' => $items,
            'aspects' => $aspects
        ];
    }

    public function shop_send(Request $r)
    {
//        if($this->user->permission != 2)
//        {
//            return [
//                'success' => false,
//                'msg' => 'Withdraw will be available after 00.00.'
//            ];
//        }

        // Проверки 


        # Наличие ссылки на трейд
        if(is_null($this->user->trade)) return [
            'success' => false,
            'msg' => $this->lang['shop']['trade_link']
        ];

        # Достижение минимального уровня для вывода
        if($this->user->lvl < $this->config->with_lvl) return [
            'success' => false,
            'msg' => $this->lang['shop']['lvl_1'].$this->config->with_lvl.$this->lang['shop']['lvl_2']
        ];

        # Максимальное кол-во предметов за один вывод
        if(count($r->get('items')) > $this->config->with_max_items) return [
            'success' => false,
            'msg' => $this->lang['shop']['max_with'].$this->config->with_max_items
        ];
        
        if(count($r->get('items')) < 1) return [
            'success' => false,
            'msg' => 'You forgot to choose items!'
        ];

        // Проверяем наличие товара на складе, а так же находим конечную сумму заказа
        $returnPrice = 0;
        $returnValue = 0;
        foreach($r->get('items') as $item)
        {
            $count = Shop::where('classid', $item['classid'])->count();
            if($count < $item['count']) return [
                'success' => false,
                'msg' => 'Could not find x'.($item['count']-$count).' '.$item['market_hash_name']
            ];

            if(isset($this->prices[$item['market_hash_name']])) 
            {
                $returnPrice += floor((($this->prices[$item['market_hash_name']] * 1000) / 100) * (100 + $this->config->with_percent));
                $returnValue += $this->prices[$item['market_hash_name']];
            }
        }

        // Проверки

        # На всякий.
        if($returnPrice < 0) return [
            'success' => false,
            'msg' => 'Something went wrong...'
        ];

        # Баланс
        if($this->user->money < $returnPrice) return [
            'success' => false,
            'msg' => 'Insufficient balance!'
        ];

        // Собираем предметы
        $items = [];
        foreach($r->get('items') as $item)
        {
            $list = Shop::where('classid', $item['classid'])->limit($item['count'])->get();
            foreach($list as $i)
            {
                if(!isset($items[$i->bot_id])) {
                    $items[$i->bot_id] = [];
                    $items[$i->bot_id]['items'] = [];
                    $items[$i->bot_id]['list'] = [];
                    $items[$i->bot_id]['price'] = 0;
                    $items[$i->bot_id]['value'] = 0;
                }
                $items[$i->bot_id]['items'][] = [
                    'market_hash_name' => $i->market_hash_name,
//                    'icon_url' => $i->icon_url,
                    'price' => $i->price,
                    'bot' => $i->bot_id
                ];

                $items[$i->bot_id]['list'][] = [
                    'appid' => 570,
                    'assetid' => $i->assetid,
                    'contextid' => 2
                ];

                $items[$i->bot_id]['price'] += floor(($i->price/100)*(100+$this->config->with_percent));
                $items[$i->bot_id]['value'] += (($i->price/(100+$this->config->with_percent))*100)/1000;
            }
        }

        // return $items;

        $withdrawID = $this->getWithdrawId();
        foreach($items as $key => $data)
        {
            DB::table('withdraws')->insert([
                'user_id' => $this->user->id,
                'bot_id' => $key,
                'with_id' => $withdrawID,
                'sendItems' => json_encode($data['list']),
                'items' => json_encode($data['items']),
                'price' => $data['price'],
                'value' => $data['value']
            ]);

            $this->user->money -= $data['price'];
            $this->user->save();
        }
        
        $this->redis->publish('updateBalance', json_encode([
            'user_id' => $this->user->id,
            'balance' => number_format($this->user->money, 0, ' ', ' ')
        ]));

        // // Снимаем баланс с пользователя
        // $this->user->money -= $returnPrice;
        // $this->user->save();

        // // Добавляем трейды в базу данных со статусом 0
        // $withdrawID = $this->getWithdrawId();
        // foreach($bots as $key => $sItems) if(isset($bots[$key]))
        // {
        //     $offerPrice = 0;
        //     $offerValue = 0;
        //     foreach($sItems['items'] as $item)
        //     {
        //         $offerPrice += floor((($this->prices[$item['market_hash_name']] * 1000) / 100) * (100 + $this->config->with_percent));
        //         $offerValue += $this->prices[$item['market_hash_name']];
        //     } 
        //     DB::table('withdraws')->insert([
        //         'user_id' => $this->user->id,
        //         'with_id' => $withdrawID,
        //         'bot_id' => $key,
        //         'sendItems' => json_encode($sItems['sendItems']),
        //         'items' => json_encode($sItems['items']),
        //         'offer_id' => null,
        //         'price' => $offerPrice,
        //         'value' => $offerValue
        //     ]);
        // }

        // Оповещаем пользователя об отправке его заказа
        return [
            'success' => true,
            'msg' => 'Your withdraw was sent to the queue!'
        ];

        // return [
        //     'res' => $r->get('items'),
        //     'price' => $returnPrice,
        //     'sendItems' => $sendItems
        // ];
    }

    private function getWithdrawId()
    {
        $last = DB::table('withdraws')->orderBy('id', 'desc')->first();
        if(is_null($last)) return 1;
        return $last->with_id+1;
    }

    private function getRarity($tags)
    {
        $tags = json_decode($tags);
        foreach($tags as $tag) if(isset($tag->category) && $tag->category == 'Rarity' && isset($tag->name)) return $tag->name;
        return null;
    }

    private function getHero($tags)
    {
        $tags = json_decode($tags);
        foreach($tags as $tag) if(isset($tag->category) && $tag->category == 'Hero' && isset($tag->name)) return $tag->name;
        return null;
    }

    private function getType($tags)
    {
        $tags = json_decode($tags);
        foreach($tags as $tag) if(isset($tag->category) && $tag->category == 'Type' && isset($tag->name)) return $tag->name;
        return null;
    }

    private function getQuality($tags)
    {
        $tags = json_decode($tags);
        foreach($tags as $tag) if(isset($tag->category) && $tag->category == 'Quality' && isset($tag->name)) return $tag->name;
        return null;
    }

    private function getAspects($items)
    {
        $items = json_decode(json_encode($items), true);

        $heroes     = [];
        $raritys    = [];
        $qualitys   = [];
        $types      = [];
        $min        = 0;
        $max        = 0;

        foreach($items as $item)
        {
            # Heroes
            $found = false;
            foreach($heroes as $hero) if(!$found && $hero == $item['hero']) $found = true;
            if(!$found) $heroes[] = $item['hero'];

            # Raritys
            $found = false;
            foreach($raritys as $rarity) if(!$found && $rarity == $item['rarity']) $found = true;
            if(!$found) $raritys[] = $item['rarity'];

            # Qualitys
            $found = false;
            foreach($qualitys as $quality) if(!$found && $quality == $item['quality']) $found = true;
            if(!$found) $qualitys[] = $item['quality'];

            # Types
            $found = false;
            foreach($types as $type) if(!$found && $type == $item['type']) $found = true;
            if(!$found) $types[] = $item['type'];
        }

        usort($items, function($a, $b) {
            return($b['price']-$a['price']);
        });

        if(isset($items[0])) $max = $items[0]['price']; else $max = 0;

        return [
            'heroes' => $heroes,
            'raritys' => $raritys,
            'qualitys' => $qualitys,
            'types' => $types,
            'min' => $min,
            'max' => $max
        ];
    }

    public function getCountAvailableTrades($id)
    {
        $w = DB::table('withdraws')->where('bot_id', $id)->where('status', 2)->count();
        $d = DB::table('deposits')->where('bot_id', $id)->where('status', 2)->count();

        if($w+$d == 30) return false;
        return true;
    }

    private function parseItems($steamID64, $is_stacks)
    {
        $res = json_decode(file_get_contents('https://api.steamapi.io/user/inventory/'.$steamID64.'/570/2?key=6bf7a917dbdacf5ec7bb488840443c99'));

        if(is_null($res)) return [
            'success' => false,
            'msg' => $this->lang['shop']['trylater']
        ];

        $items = [];
        foreach($res as $assetid => $item) {
            if($item->tradable && $item->marketable) {
                $price = null;
                $name = str_replace('Autographed ', '', $item->market_hash_name);
                if(isset($this->prices[$name])) $price = $this->prices[$name];
                if(!is_null($price)) {
                    $items[] = [
                        'assetid' => $item->assetid,
                        'market_hash_name' => $item->market_hash_name,
                        'classid' => $item->classid,
                        'icon_url' => str_replace('\n', '', $item->icon_url),
                        'instanceid' => $item->instanceid,
                        'price' => floor($price*$this->config->shop_curs),
                        'rarity' => $this->getRarity(json_encode($item->tags)),
                        'hero' => $this->getHero(json_encode($item->tags)),
                        'type' => $this->getType(json_encode($item->tags)),
                        'quality' => $this->getQuality(json_encode($item->tags))
                    ];
                }
            }
        }

        $bot = DB::table('bots')->where('steamid64', $steamID64)->first();
        if(is_null($bot)) return ['success' => true, 'items' => $items];

        $list = [];
        foreach($items as $item) if(!is_null($item)) $list[] = $item;

        return ['success' => true, 'items' => $list];
    }

    public function getBots()
    {
        $bots = DB::table('bots')->get();
        if(count($bots) < 1) return [
            'success' => false,
            'msg' => 'Не удалось найти ботов!'
        ];

        return [
            'success' => true,
            'bots' => $bots
        ];
    }

    public function updateTable()
    {
        Shop::truncate();
        $bots = DB::table('bots')->get();
        foreach($bots as $bot) {
            $items = $this->parseItems($bot->steamid64, false);
            if(!$items['success']) return 'false';

            $wList = DB::table('withdraws')->where('status', 0)->get();
            foreach($wList as $w)
            {
                $wItems = json_decode($w->sendItems);

                foreach($wItems as $item) foreach($items['items'] as $key => $i)
                {
                    if($item->assetid == $i['assetid'])
                    {
                        unset($items['items'][$key]);
                    }
                }
            }

            // Добавляем предметы в базу данных.
            foreach($items['items'] as $i)
            {
                if(!is_null($i) && isset($this->prices[$i['market_hash_name']])) {
                    Shop::insert([
                        'bot_id' => $bot->id,
                        'market_hash_name' => $i['market_hash_name'],
                        'icon_url' => $i['icon_url'],
                        'classid' => $i['classid'],
                        'assetid' => $i['assetid'],
                        'type' => $i['type'],
                        'quality' => $i['quality'],
                        'hero' => $i['hero'],
                        'rarity' => $i['rarity'],
                        'price' => floor($this->prices[$i['market_hash_name']]*1000)
                    ]);
                }
            }
        }

        return 'success';
    }

    public function updatePrice()
    {
        $res = json_decode(file_get_contents('https://api.steamapi.io/market/prices/570?key=6bf7a917dbdacf5ec7bb488840443c99'), true);
        $array = [];
        foreach($res as $name => $value) {
            if(json_encode(strpos($name, 'Autographed')) == '0')
            {
                $hashName = str_replace('Autographed ', '', $name);
                if(isset($res[$hashName])) {
                	if($res[$hashName] > $value)
                	{
                		$array[$name] = $value;
                	} 
                	else
                	{
                		$array[$name] = $res[$hashName];
                	}
                }
            } elseif (json_encode(strpos($name, 'Corrupted')) == '0')
            {
                $hashName = str_replace('Corrupted ', '', $name);
                if(isset($res[$hashName])) {
                	if($res[$hashName] > $value)
                	{
                		$array[$name] = $value;
                	} 
                	else
                	{
                		$array[$name] = $res[$hashName];
                	}
                }
            } elseif (json_encode(strpos($name, 'Inscribed')) == '0')
            {
                $hashName = str_replace('Inscribed ', '', $name);
                if(isset($res[$hashName])) {
                	if($res[$hashName] > $value)
                	{
                		$array[$name] = $value;
                	} 
                	else
                	{
                		$array[$name] = $res[$hashName];
                	}
                }
            } elseif (json_encode(strpos($name, 'Auspicious')) == '0')
            {
                $hashName = str_replace('Auspicious ', '', $name);
                if(isset($res[$hashName])) {
                	if($res[$hashName] > $value)
                	{
                		$array[$name] = $value;
                	} 
                	else
                	{
                		$array[$name] = $res[$hashName];
                	}
                }
            } elseif (json_encode(strpos($name, 'Heroic')) == '0')
            {
                $hashName = str_replace('Heroic ', '', $name);
                if(isset($res[$hashName])) {
                	if($res[$hashName] > $value)
                	{
                		$array[$name] = $value;
                	} 
                	else
                	{
                		$array[$name] = $res[$hashName];
                	}
                }
            } elseif (json_encode(strpos($name, 'Genuine')) == '0')
            {
                $hashName = str_replace('Genuine ', '', $name);
                if(isset($res[$hashName])) {
                	if($res[$hashName] > $value)
                	{
                		$array[$name] = $value;
                	} 
                	else
                	{
                		$array[$name] = $res[$hashName];
                	}
                }
            } elseif (json_encode(strpos($name, 'Frozen')) == '0')
            {
                $hashName = str_replace('Frozen ', '', $name);
                if(isset($res[$hashName])) {
                	if($res[$hashName] > $value)
                	{
                		$array[$name] = $value;
                	} 
                	else
                	{
                		$array[$name] = $res[$hashName];
                	}
                }
            } elseif (json_encode(strpos($name, 'Cursed')) == '0')
            {
                $hashName = str_replace('Cursed ', '', $name);
                if(isset($res[$hashName])) {
                	if($res[$hashName] > $value)
                	{
                		$array[$name] = $value;
                	} 
                	else
                	{
                		$array[$name] = $res[$hashName];
                	}
                }
            } elseif (json_encode(strpos($name, 'Unusual')) == '0')
            {
                $hashName = str_replace('Unusual ', '', $name);
                if(isset($res[$hashName])) {
                	if($res[$hashName] > $value)
                	{
                		$array[$name] = $value;
                	} 
                	else
                	{
                		$array[$name] = $res[$hashName];
                	}
                }
            } elseif (json_encode(strpos($name, 'Infused')) == '0')
            {
                $hashName = str_replace('Infused ', '', $name);
                if(isset($res[$hashName])) {
                	if($res[$hashName] > $value)
                	{
                		$array[$name] = $value;
                	} 
                	else
                	{
                		$array[$name] = $res[$hashName];
                	}
                }
            } elseif (json_encode(strpos($name, 'Exalted')) == '0')
            {
                $hashName = str_replace('Exalted ', '', $name);
                if(isset($res[$hashName])) {
                	if($res[$hashName] > $value)
                	{
                		$array[$name] = $value;
                	} 
                	else
                	{
                		$array[$name] = $res[$hashName];
                	}
                }
            } elseif (json_encode(strpos($name, 'Elder')) == '0')
            {
                $hashName = str_replace('Elder ', '', $name);
                if(isset($res[$hashName])) {
                	if($res[$hashName] > $value)
                	{
                		$array[$name] = $value;
                	} 
                	else
                	{
                		$array[$name] = $res[$hashName];
                	}
                }
            } elseif (json_encode(strpos($name, 'Legacy')) == '0')
            {
                $hashName = str_replace('Legacy ', '', $name);
                if(isset($res[$hashName])) {
                	if($res[$hashName] > $value)
                	{
                		$array[$name] = $value;
                	} 
                	else
                	{
                		$array[$name] = $res[$hashName];
                	}
                }
            } else 
            {
                $array[$name] = $value;
            }
        }

        \Storage::disk('public')->put('file.txt', json_encode($array));
        return 'success';
    }

    public function updateInventory(Request $r)
    {
        $bot = DB::table('bots')->where('steamid64', $r->get('steamid64'))->first();
        if(is_null($bot)) return ['success' => false];
        $items = Shop::where('bot_id', $bot->id)->get();
        $res = $this->parseItems($bot->steamid64, false);
        if(!$res['success']) return ['success' => false];

        $list = DB::table('withdraws')->where('bot_id', $bot->id)->where('status', '<', 4)->get();
        foreach($list as $w)
        {
            $lItems = json_decode($w->sendItems);
            foreach($lItems as $lItem) foreach($res['items'] as $key => $item) if($res['items'][$key]['assetid'] == $lItem->assetid) unset($res['items'][$key]);
        }

        foreach($res['items'] as $i) if(!is_null($i)) {
            $found = false;
            foreach($items as $key => $item) if(!$found && !is_null($item) && $item->classid == $i['classid'] && $item->assetid == $i['assetid']) {
                $found = true;
                unset($items[$key]);
            }
            if(!$found) {
                Shop::insert([
                    'bot_id' => $bot->id,
                    'market_hash_name' => $i['market_hash_name'],
                    'icon_url' => $i['icon_url'],
                    'classid' => $i['classid'],
                    'assetid' => $i['assetid'],
                    'type' => $i['type'],
                    'quality' => $i['quality'],
                    'hero' => $i['hero'],
                    'rarity' => $i['rarity'],
                    'price' => $i['price']
                ]);
            } else {
                Shop::where('classid', $i['classid'])->where('assetid', $i['assetid'])->update(['price' => $i['price']]);
            }
        }

        foreach($items as $item) if(!is_null($item)) Shop::where('id', $item['id'])->delete();

        return ['success' => true];
    }

    public function admin_send($r)
    {
        $owner = DB::table('bots')->where('type', 1)->first();
        $list = Shop::where('bot_id', '!=', $owner->id)->get();
        $items = $r->get('items');
        $itemsList = [];
        $price = 0;
        foreach($items as $key => $item) foreach($list as $n => $l) if(!is_null($list[$n]) && $l->classid == $item['classid']) {
            if($items[$key]['count'] > 0 && !is_null($list[$n]) && $list[$n]->classid == $item['classid']) {
                $itemsList[] = [
                    'id' => $l->id,
                    'bot' => $l->bot_id,
                    'assetid' => $l->assetid,
                    'classid' => $l->classid,
                    'market_hash_name' => $l->market_hash_name,
                    'icon_url' => $l->icon_url,
                    'price' => $l->price
                ];
                $price += $l->price;
                $list[$n] = NULL;
                $items[$key]['count']--;
            }
        }

        foreach($items as $key => $item) if($item['count'] > 0) return [
            'success' => false,
            'msg' => 'Не удалось найти нужное кол-во итемов на ботах!'
        ];

        $bots = [];
        foreach($itemsList as $item) {
            if(!isset($bots[$item['bot']])) $bots[$item['bot']] = [];
            $bots[$item['bot']][] = [
                'classid' => $item['classid'],
                'assetid' => $item['assetid'],
                'market_hash_name' => $item['market_hash_name'],
                'icon_url' => $item['icon_url'],
                'price' => $item['price']
            ];
        }

        $with_id = $this->getWithdrawId();

        $owner = DB::table('bots')->where('type', 1)->first();
        foreach($bots as $id => $items) {
            if(!is_null($bots[$id]) && $id != $owner->id) {
                DB::table('withdraws')->insert([
                    'user_id' => $this->user->steamid64,
                    'with_id' => $with_id,
                    'bot_id' => $id,
                    'items' => json_encode($items),
                    'price' => floor(($price/100)*(100+$this->config->with_percent)),
                    'value' => ($price/$this->config->shop_curs)
                ]);
            }
        }

        foreach($itemsList as $item) Shop::where('id', $item['id'])->delete();

        $this->redis->publish('withdraw', true);

        return [
            'success' => true,
            'msg' => 'Ваш вывод обрабатывается!',
            'balance' => $this->user->money
        ];
    }

    public function admin_parse()
    {
        $items = Shop::groupBy('classid')->get();
        foreach($items as $key => $item) $items[$key]->count = Shop::where('classid', $item->classid)->count();
        foreach($items as $key => $item) {
            $items[$key]['price'] = (($items[$key]['price']/100)*(100+$this->config->with_percent));
        }

        $aspects = $this->getAspects($items);

        return [
            'success' => true,
            'items' => $items,
            'aspects' => $aspects
        ];
    }

    public function checkDeposits(Request $r)
    {
        // if(!$this->getCountAvailableTrades($r->get('id'))) return [
        //     'success' => false,
        //     'msg' => 'Бот отправил максимальное кол-во обменов. Ждем, пока место освободится.'
        // ];

        $offer = DB::table('deposits')->where('bot_id', $r->get('id'))->where('status', 0)->orderBy('id', 'asc')->first();
        if(is_null($offer)) return ['success' => false, 'msg' => 'Не удалось найти последний депозит!'];

        $user = User::where('id', $offer->user_id)->first();
        if(is_null($user)) 
        {
            // Тут отменяем трейд и подаем сигнал заного
            DB::table('deposits')->where('id', $offer->id)->update([
                'status' => 4
            ]);

            $this->redis->publish('deposit', json_encode([
                'id' => $offer->bot_id
            ]));

            return ['success' => false, 'msg' => 'Отменили депозит : не удалось найти его владельца.'];
        }

        DB::table('deposits')->where('id', $offer->id)->update([
            'status' => 1
        ]);

        return [
            'success' => true,
            'sendItems' => json_decode($offer->sendItems),
            'id' => $offer->id,
            'trade' => $user->trade,
	        'user_id' => $user->id
        ];
    }

    public function checkWithdraws(Request $r)
    {
         if(!$this->getCountAvailableTrades($r->get('id'))) return [
             'success' => false,
             'msg' => 'Бот отправил максимальное кол-во обменов. Ждем, пока место освободится.'
         ];

        $offer = DB::table('withdraws')->where('bot_id', $r->get('id'))->where('status', 0)->orderBy('id', 'asc')->first();
        if(is_null($offer)) return ['success' => false, 'msg' => 'Не удалось найти последний вывод!'];

        $user = User::where('id', $offer->user_id)->first();
        if(is_null($user)) 
        {
            // Тут отменяем трейд и подаем сигнал заного
            DB::table('withdraws')->where('id', $offer->id)->update([
                'status' => 4
            ]);

            return ['success' => false, 'msg' => 'Отменили депозит : не удалось найти его владельца.'];
        }
        
        DB::table('withdraws')->where('id', $offer->id)->update([
            'status' => 1
        ]);

        return [
            'success' => true,
            'sendItems' => json_decode($offer->sendItems),
            'id' => $offer->with_id,
            'trade' => $user->trade,
	        'user_id' => $user->id
        ];
    }

    public function updateWithdrawStatus(Request $r)
    {
        if($r->get('status') == 2)
        {
            DB::table('withdraws')->where('with_id', $r->get('id'))->where('bot_id', $r->get('bot'))->update([
                'offer_id' => $r->get('offer_id'),
                'status' => $r->get('status')
            ]);
        } else {
            DB::table('withdraws')->where('offer_id', $r->get('offer_id'))->update([
                'status' => $r->get('status')
            ]);   

            if($r->get('status') == 5)
            {
                $offer = DB::table('withdraws')->where('with_id', $r->get('id'))->where('bot_id', $r->get('bot'))->first();
                if(is_null($offer))
                {
                    $offer = DB::table('withdraws')->where('offer_id', $r->get('offer_id'))->first();
                    if(is_null($offer)) return [
                        'success' => false,
                        'msg' => 'Не удалось найти вывод #'.$r->get('offer_id')
                    ];
                }

                if(!is_null($offer))
                {
                    $user = User::where('id', $offer->user_id)->first();
                    if(!is_null($user))
                    {
                        $user->money += $offer->price;
                        $user->save();
                        
                        $this->redis->publish('updateBalance', json_encode([
                            'user_id' => $user->id,
                            'balance' => number_format($user->money, 0, ' ', ' ')
                        ]));
                    }
                }
            }
        }
        
        return ['success' => true];
    }

    public function updateDepositStatus(Request $r)
    {
        if($r->get('status') == 2)
        {
            DB::table('deposits')->where('id', $r->get('id'))->where('bot_id', $r->get('bot'))->update([
                'offer_id' => $r->get('offer_id'),
                'status' => $r->get('status')
            ]);
        } else {
            DB::table('deposits')->where('offer_id', $r->get('offer_id'))->update([
                'status' => $r->get('status')
            ]);  
            if($r->get('status') == 3)
            {
                // Принят
                $offer = DB::table('deposits')->where('offer_id', $r->get('offer_id'))->first();
                if(!is_null($offer))
                {
                    $user = User::where('id', $offer->user_id)->first();
                    if(!is_null($user))
                    {
                        $user->money += $offer->price;
                        $user->save();
                        
                        $this->redis->publish('updateBalance', json_encode([
                            'user_id' => $user->id,
                            'balance' => number_format($user->money, 0, ' ', ' ')
                        ]));
                    }
                }
            } 
        }
        
        return ['success' => true];
    }
    
    public function sendNotify(Request $r)
    {
        $offer = DB::table('withdraws')->where('offer_id', $r->get('id'))->first();
        if(is_null($offer)) $offer = DB::table('deposits')->where('offer_id', $r->get('id'))->first();
        if(is_null($offer)) return [
            'success' => true,
            'msg' => 'Не удалось найти вывод #'.$r->get('id')
        ];
        
        $user = User::where('id', $offer->user_id)->first();
        if(is_null($user)) return [
            'success' => false,
            'msg' => 'Не удалось найти владельца вывода #'.$offer->offer_id
        ];
        
        // Отправляем оповещение.
        $this->redis->publish('message', json_encode([
            'user_id' => $user->id,
            'msg' => 'You must accept the sent exchange offer within 4 minutes.',
            'type' => 'info'
        ]));
        
        return [
            'success' => true
        ];
    }

    public function declineWithdraw(Request $r)
    {
        $offer = DB::table('withdraws')->where('with_id', $r->get('id'))->where('bot_id', $r->get('bot'))->first();
        if(is_null($offer)) return [
            'success' => false,
            'msg' => 'Не удалось найти вывод #'.$r->get('id')
        ];

        DB::table('withdraws')->where('with_id', $r->get('id'))->where('bot_id', $r->get('bot'))->update([
            'status' => 4
        ]);

        $user = User::where('id', $offer->user_id)->first();
        if(!is_null($user))
        {
            $user->money += $offer->price;
            $user->save();
            
            $this->redis->publish('updateBalance', json_encode([
                'user_id' => $user->id,
                'balance' => number_format($user->money, 0, ' ', ' ')
            ]));
        }

        return [
            'success' => true,
            'msg' => 'Отменили вывод #'.$r->get('id')
        ];
    }

    public function declineDeposit(Request $r)
    {
        $offer = DB::table('deposits')->where('id', $r->get('id'))->first();
        if(is_null($offer)) return [
            'success' => false,
            'msg' => 'Не удалось найти депозит #'.$r->get('id')
        ];

        DB::table('deposits')->where('id', $r->get('id'))->update([
            'status' => 4
        ]);

        return [
            'success' => true,
            'msg' => 'Отменили депозит #'.$r->get('id')
        ];
    }

    public function getOffers(Request $r)
    {
        $list = [];

        $withdraws = DB::table('withdraws')->where('status', 2)->where('bot_id', $r->get('bot'))->get();
        $deposits = DB::table('deposits')->where('status', 2)->where('bot_id', $r->get('bot'))->get();

        foreach($withdraws as $w) $list[] = $w->offer_id;
        foreach($deposits as $w) $list[] = $w->offer_id;

        return $list;
    }

    // public function test()
    // {
    //     $list = DB::table('withdraws')->where('status', '<', 2)->get();
    //     $money = 0;
    //     foreach($list as $w)
    //     {
    //         $do = false;
    //         $count = DB::table('withdraws_offers')->where('with_id', $w->with_id)->get();
    //         if(count($count) < 2)
    //         {
    //             $do = true;
    //         } else {
    //             $offer = DB::table('withdraws_offers')->where('with_id', $w->with_id)->where('bot_id', 4)->where('status', 1)->first();
    //             if(is_null($offer))
    //             {
    //                 $do = true;
    //             }
    //         }

    //         if($do)
    //         {
    //             $price = DB::table('withdraws')->where('with_id', $w->with_id)->where('price', '>', 0)->first();
    //             if(!is_null($price))
    //             {
    //                 $user = User::where('steamid64', $price->user_id)->first();
    //                 if(!is_null($user))
    //                 {
    //                     // $user->money += $price->price;
    //                     // $user->save();
    //                     $money += $price->price;

    //                     DB::table('withdraws_offers')->where('with_id', $w->with_id)->update([
    //                         'status' => 2
    //                     ]);
    //                     DB::table('withdraws')->where('with_id', $w->with_id)->update([
    //                         'status' => 2
    //                     ]);
    //                 }
    //             }
    //         }
    //     }

    //     return 'success ('.$money.')';
    // }

    public function test()
    {
        echo 'DOUBLE <br><br>';

        $bandit = DB::table('double_bets')->where('user_id', 4915)->get();
        foreach($bandit as $game)
        {
            echo $game->value.' -> '.$game->winner_value.' ('.$game->type.')<br>';
        }

        echo '<br><br>BANDIT <br><br>';

        $bandit = DB::table('bandit')->where('user_id', 4915)->get();
        foreach($bandit as $game)
        {
            echo $game->value.' -> '.$game->winner_value.' ('.$game->wins_lines.'/'.$game->lines.')<br>';
        }

        echo '<br><br>POKER <br><br>';

        $bandit = DB::table('poker')->where('user_id', 4915)->get();
        foreach($bandit as $game)
        {
            echo $game->value.' -> '.$game->winner_value.'<br>';
        }
    }
}
