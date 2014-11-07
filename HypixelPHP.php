<?php

/**
 * HypixelPHP
 *
 * @author Plancke
 * @version 1.0.0
 * @link  http://plancke.nl
 *
 */
class HypixelPHP
{
    private $options;

    public function  __construct($input = array())
    {
        $this->options = array_merge(
            array(
                'api_key'              => '',
                'cache_time'           => '600',
                'cache_folder_player'  => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/player/',
                'cache_uuid_table'     => 'uuid_table.json',
                'cache_folder_guild'   => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/guild/',
                'cache_byPlayer_table' => 'byPlayer_table.json',
                'cache_byName_table'   => 'byName_table.json'
            ),
            $input
        );

        if(!file_exists($this->options['cache_folder_player'])) {
            mkdir($this->options['cache_folder_player'], 0777, true);
        }

        if(!file_exists($this->options['cache_folder_guild'])) {
            mkdir($this->options['cache_folder_guild'], 0777, true);
        }
    }

    public function set($input)
    {
        foreach($input as $key=>$val)
        {
            $this->options[$key] = $val;
        }
    }

    public function setKey($key)
    {
        $this->options['api_key'] = $key;
    }
    public function getKey()
    {
        return $this->options['api_key'];
    }

    public function fetch($request, $key, $val)
    {
        $response = @file_get_contents('https://api.hypixel.net/' . $request . '?key=' . $this->getKey() . '&' . $key . '=' . $val);
        return json_decode($response, true);
    }

    public function getPlayer($keypair = array())
    {
        $pairs = array_merge(
            array(
                'name' => '',
                'uuid' => ''
            ),
            $keypair
        );

        foreach ($pairs as $key => $val) {
            $val = strtolower($val);
            if ($val != '') {
                if ($key == 'uuid') {
                    $filename = $this->options['cache_folder_player'] . $this->options['cache_uuid_table'];
                    if (!file_exists($filename)) {
                        $file = fopen($filename, 'w');
                        fwrite($file, json_encode(array()));
                        fclose($file);
                        $content = array();
                    }
                    else
                    {
                        $file = fopen($filename, 'r');
                        $content = json_decode(fread($file, filesize($filename)), true);
                        fclose($file);
                    }

                    if(array_key_exists($val, $content))
                    {
                        if(time() - $this->options['cache_time'] < $content[$val]['timestamp'])
                        {
                            // get cache
                            return $this->getPlayer(array('name'=>$content[$val]['name']));
                        }
                    }

                    // new/update entry
                    $response = $this->fetch('player', $key, $val);
                    if ($response['success']) {
                        $content[$val] = array('timestamp'=>time(), 'name'=>$response['player']['displayname']);
                        $file = fopen($filename, 'w');
                        fwrite($file, json_encode($content));
                        fclose($file);

                        return new Player($response, $this->options['api_key']);
                    }

                }

                if ($key == 'name') {
                    $filename = $this->options['cache_folder_player'] . $key . '/' . $this->getCacheFileName($val) . '.json';
                    if (file_exists($filename)) {
                        if (time() - $this->options['cache_time'] < filemtime($filename)) {
                            // get cache
                            $file = fopen($filename, 'r');
                            $content = fread($file, filesize($filename));
                            fclose($file);

                            $json = json_decode($content, true);
                            return new Player($json['player'], $this->options['api_key']);
                        }
                    }
                    else
                    {
                        @mkdir(dirname($filename), 0777, true);
                    }

                    // new/update entry
                    $response = $this->fetch('player', $key, $val);
                    if ($response['success']) {
                        $file = fopen($filename, 'w');
                        fwrite($file, json_encode($response));
                        fclose($file);

                        return new Player($response['player'], $this->options['api_key']);
                    }
                }
            }
        }

        return null;
    }

    public function getGuild($keypair = array())
    {
        $pairs = array_merge(
            array(
                'byPlayer' => '',
                'byName'   => '',
                'id'       => ''
            ),
            $keypair
        );

        foreach ($pairs as $key => $val) {
            if ($val != '') {
                if ($key == 'byPlayer' || $key == 'byName') {
                    $filename = $this->options['cache_folder_guild'] . $this->options['cache_' . $key . '_table'];
                    if (!file_exists($filename)) {
                        $file = fopen($filename, 'w');
                        fwrite($file, json_encode(array()));
                        fclose($file);
                        $content = array();
                    } else {
                        $file = fopen($filename, 'r');
                        $content = json_decode(fread($file, filesize($filename)), true);
                        fclose($file);
                    }

                    if (array_key_exists($val, $content)) {
                        if (time() - $this->options['cache_time'] < $content[$val]['timestamp']) {
                            // get cache
                            return $this->getGuild(array('id' => $content[$val]['guild']));
                        }
                    }

                    // new/update entry
                    $response = $this->fetch('findGuild', $key, $val);
                    if ($response['success']) {
                        $content[$val] = array('timestamp' => time(), 'guild' => $response['guild']);
                        $file = fopen($filename, 'w');
                        fwrite($file, json_encode($content));
                        fclose($file);

                        return $this->getGuild(array('id' => $response['guild']));
                    }
                }

                if ($key == 'id') {
                    $filename = $this->options['cache_folder_guild'] . $key . '/' . $val . '.json';
                    if (file_exists($filename)) {
                        if (time() - $this->options['cache_time'] < filemtime($filename)) {
                            // get cache
                            $file = fopen($filename, 'r');
                            $content = fread($file, filesize($filename));
                            fclose($file);

                            $json = json_decode($content, true);
                            return new Guild($json['guild'], $this->options['api_key']);
                        }
                    }
                    else
                    {
                        @mkdir(dirname($filename), 0777, true);
                    }

                    // new/update entry
                    $response = $this->fetch('guild', $key, $val);
                    if ($response['success']) {
                        $file = fopen($filename, 'w');
                        fwrite($file, json_encode($response));
                        fclose($file);

                        return new Guild($response['guild'], $this->options['api_key']);
                    }
                }
            }
        }
        return null;
    }

    private function getCacheFileName($input)
    {
        if(strlen($input) < 3 )
        {
            return implode('/', str_split($input, 1));
        }

        return substr($input, 0, 1) . '/' . substr($input, 1, 1) . '/' . substr($input, 2);

    }

}

class HypixelObject
{
    public $infojson;
    public $apiKey;

    public function __construct($json, $apiKey)
    {
        $this->infojson = $json;
        $this->apiKey = $apiKey;
    }

    public function getRaw()
    {
        return $this->infojson;
    }

    public function get($key, $implicit = false, $default = null)
    {
        if(!$implicit)
        {
            $return = $this->infojson;
            foreach(explode(".", $key) as $split)
            {
                if(in_array($split, array_keys($return))) {
                    $return = $return[$split];
                } else {
                    return $default;
                }

            }
            return $return ? $return : $default;
        }
        return in_array($key, array_keys($this->infojson)) ? $this->infojson[$key] : $default;
    }

    public function getId()
    {
        return $this->get('_id', true);
    }
}

class Player extends HypixelObject
{

    public function getSession()
    {
        $HypixelPHP = new HypixelPHP(array('api_key'=>$this->apiKey));
        return $HypixelPHP->fetch('session', 'player', $this->getName());
    }

    public function getName()
    {
        if($this->get('displayname', true))
        {
            return $this->get('displayname', true);
        }
        else
        {
            $aliases = $this->get('knownAliases', true);
            return $aliases[0];
        }
    }

    public function getStats()
    {
        return new Stats($this->get('stats', true));
    }

    public function isPreEULA()
    {
        return $this->get('eulaCoins', true);
    }

    public function getLevel()
    {
        return $this->get('networkLevel', true) + 1;
    }

    public function getBooster()
    {
        $ranks = array('DEFAULT', 'VIP', 'VIP+', 'MVP', 'MVP+');
        $pre = $this->getRank(true, true);

        $flip = array_flip($ranks);
        $rankKey = $flip[$pre] + 1;
        $levelkey = floor($this->getLevel() / 25) + 1;

        return ($rankKey > $levelkey) ? $rankKey : $levelkey;
    }

    public function getRank($package = true, $preEULA = false)
    {
        if($package)
        {
            $keys = array('newPackageRank', 'packageRank');
            if($preEULA) $keys = array_reverse($keys);
            $rank = $this->get('rank', true);
            if($rank == 'NORMAL')
            {
                foreach($keys as $key)
                {
                    if($this->get($key, true))
                    {
                        return str_replace('_PLUS', '+', $this->get($key, true));
                    }
                }
            }
            else
            {
                if($this->get($keys[0], true))
                {
                    return str_replace('_PLUS', '+', $this->get($keys[0], true));
                }
            }
            return 'DEFAULT';
        }
        else
        {
            $rank = $this->get('rank', true);
            if($rank == 'NORMAL' || $rank == null)
            {
                return $this->getRank(true, $preEULA);
            }
            return $rank;
        }
    }
}

class Stats extends HypixelObject
{
    public function __construct($json)
    {
        $this->infojson = $json;
    }

    public function getGame($game)
    {
        return new GameStats(isset($this->infojson[$game]) ? $this->infojson[$game] : null);
    }
}

class GameStats extends HypixelObject
{
    public function __construct($json)
    {
        $this->infojson = $json;
    }

    public function get($field, $default = null)
    {
        if($this->infojson == null)
            return $default;
        return in_array($field, array_keys($this->infojson)) ? $this->infojson[$field] : $default;
    }
}

class Guild extends HypixelObject
{
    private $members;

    public function getName()
    {
        return $this->get('name', true);
    }

    public function canTag()
    {
        return $this->get('canTag', true);
    }

    public function getTag()
    {
        return $this->get('tag', true);
    }

    public function getCoins()
    {
        return $this->get('coins', true);
    }

    public function getMemberList()
    {
        if($this->members != null)
            return $this->members;
        $this->members = new MemberList($this->infojson['members']);
        return $this->getMemberList();
    }
}

class MemberList
{
    private $list;

    public function __construct($json)
    {
        $list = array("GUILDMASTER"=>array(), "OFFICER"=>array(), "MEMBER"=>array());

        foreach($json as $player)
        {
            $rank = $player['rank'];
            if(!in_array($rank, array_keys($list)))
            {
                $list[$rank] = array();
            }

            array_push($list[$rank], $player);
        }

        $this->list = $list;
    }

    public function getList()
    {
        return $this->list;
    }
}