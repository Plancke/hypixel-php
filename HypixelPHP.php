<?php

/**
 * HypixelPHP
 *
 * @author Plancke
 * @version 1.1.0
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
                'api_key'               => '',
                'cache_time'            => '600',
                'cache_folder_player'   => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/player/',
                'cache_uuid_table'      => 'uuid_table.json',
                'cache_folder_guild'    => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/guild/',
                'cache_byPlayer_table'  => 'byPlayer_table.json',
                'cache_byName_table'    => 'byName_table.json',
                'cache_folder_friends'  => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/friends/',
                'cache_folder_sessions' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/sessions/',
                'version'               => '1.1'
            ),
            $input
        );

        if(!file_exists($this->options['cache_folder_player'])) {
            mkdir($this->options['cache_folder_player'], 0777, true);
        }

        if(!file_exists($this->options['cache_folder_guild'])) {
            mkdir($this->options['cache_folder_guild'], 0777, true);
        }

        if(!file_exists($this->options['cache_folder_friends'])) {
            mkdir($this->options['cache_folder_friends'], 0777, true);
        }

        if(!file_exists($this->options['cache_folder_sessions'])) {
            mkdir($this->options['cache_folder_sessions'], 0777, true);
        }
    }

    public function set($input)
    {
        foreach($input as $key=>$val)
        {
            $this->options[$key] = $val;
        }
    }

    public function getVersion()
    {
        return $this->options['version'];
    }

    public function setKey($key)
    {
        $this->options['api_key'] = $key;
    }
    public function getKey()
    {
        return $this->options['api_key'];
    }

    public function getOptions()
    {
        return $this->options;
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
                    $content = json_decode($this->getContent($filename), true);

                    if(array_key_exists($val, $content))
                    {
                        if(time() - $this->options['cache_time'] < $content[$val]['timestamp'])
                        {
                            return $this->getPlayer(array('name'=>$content[$val]['name']));
                        }
                    }

                    $response = $this->fetch('player', $key, $val);
                    if ($response['success']) {
                        $content[$val] = array('timestamp'=>time(), 'name'=>$response['player']['displayname']);
                        $this->setContent($filename, json_encode($content));
                        return new Player($response['player'], $this);
                    }

                }

                if ($key == 'name') {
                    $filename = $this->options['cache_folder_player'] . $key . '/' . $this->getCacheFileName($val) . '.json';

                    if (file_exists($filename)) {
                        if (time() - $this->options['cache_time'] < filemtime($filename)) {
                            $content = json_decode($this->getContent($filename), true);
                            return new Player($content, $this);
                        }
                    } else {
                        @mkdir(dirname($filename), 0777, true);
                    }

                    $response = $this->fetch('player', $key, $val);
                    if ($response['success']) {
                        $this->setContent($filename, json_encode($response['player']));
                        return new Player($response['player'], $this);
                    }
                }
            }
        }
        return new Player(null, $this);
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
                $val = str_replace(' ', '%20', $val);
                if ($key == 'byPlayer' || $key == 'byName') {
                    $filename = $this->options['cache_folder_guild'] . $this->options['cache_' . $key . '_table'];
                    $content = json_decode($this->getContent($filename), true);

                    if (array_key_exists($val, $content)) {
                        if (time() - $this->options['cache_time'] < $content[$val]['timestamp']) {
                            return $this->getGuild(array('id' => $content[$val]['guild']));
                        }
                    }

                    // new/update entry
                    $response = $this->fetch('findGuild', $key, $val);
                    if ($response['success']) {
                        $content[$val] = array('timestamp' => time(), 'guild' => $response['guild']);
                        $this->setContent($filename, json_encode($content));
                        return $this->getGuild(array('id' => $response['guild']));
                    }
                }

                if ($key == 'id') {
                    $filename = $this->options['cache_folder_guild'] . $key . '/' . $val . '.json';
                    if (file_exists($filename)) {
                        if (time() - $this->options['cache_time'] < filemtime($filename)) {
                            $content = json_decode($this->getContent($filename), true);
                            return new Guild($content['guild'], $this);
                        }
                    } else {
                        @mkdir(dirname($filename), 0777, true);
                    }

                    // new/update entry
                    $response = $this->fetch('guild', $key, $val);
                    if ($response['success']) {
                        $this->setContent($filename, json_encode($response));
                        return new Guild($response['guild'], $this);
                    }
                }
            }
        }
        return new Guild(null, $this);
    }
    public function getSession($keypair = array())
    {
        $pairs = array_merge(
            array(
                'player' => ''
            ),
            $keypair
        );

        foreach ($pairs as $key => $val) {
            $val = strtolower($val);
            if ($val != '') {
                if ($key == 'player') {
                    $filename = $this->options['cache_folder_sessions'] . $key . '/' . $this->getCacheFileName($val) . '.json';
                    if (file_exists($filename)) {
                        if (time() - $this->options['cache_time'] < filemtime($filename)) {
                            $content = $this->getContent($filename);
                            $json = json_decode($content, true);
                            return new Session($json, $this);
                        }
                    } else {
                        @mkdir(dirname($filename), 0777, true);
                    }

                    $response = $this->fetch('session', $key, $val);
                    if ($response['success']) {
                        $this->setContent($filename, json_encode($response['session']));
                        return new Session($response['session'], $this);
                    }
                }
            }
        }
        return new Session(null, $this);;
    }
    public function getFriends($keypair = array())
    {
        $pairs = array_merge(
            array(
                'player' => ''
            ),
            $keypair
        );

        foreach ($pairs as $key => $val) {
            $val = strtolower($val);
            if ($val != '') {
                if ($key == 'player') {
                    $filename = $this->options['cache_folder_friends'] . $key . '/' . $this->getCacheFileName($val) . '.json';
                    if (file_exists($filename)) {
                        if (time() - $this->options['cache_time'] < filemtime($filename)) {
                            $content = $this->getContent($filename);
                            $json = json_decode($content, true);
                            return new Session($json, $this);
                        }
                    } else {
                        @mkdir(dirname($filename), 0777, true);
                    }

                    $response = $this->fetch('friends', $key, $val);
                    if ($response['success']) {
                        $this->setContent($filename, json_encode($response['records']));
                        return new Session($response['records'], $this);
                    }
                }
            }
        }
        return new Friends(null, $this);
    }

    private function getContent($filename)
    {
        @mkdir(dirname($filename), 0777, true);
        $content = json_encode(array());
        if (!file_exists($filename)) {
            $file = fopen($filename, 'w');
            fwrite($file, $content);
            fclose($file);
        } else {
            $file = fopen($filename, 'r');
            $content = fread($file, filesize($filename));
            fclose($file);
        }
        return $content;
    }

    private function setContent($filename, $content)
    {
        @mkdir(dirname($filename), 0777, true);
        $file = fopen($filename, 'w');
        fwrite($file, $content);
        fclose($file);
    }

    private function getCacheFileName($input)
    {
        if(strlen($input) < 3 ) {
            return implode('/', str_split($input, 1));
        }
        return substr($input, 0, 1) . '/' . substr($input, 1, 1) . '/' . substr($input, 2);
    }

}

class HypixelObject {
    public $JSONArray;
    public $api;

    public function __construct($json, $api)
    {
        $this->JSONArray = $json;
        $this->api = $api;
    }

    public function isNull() {
        return $this->getRaw() == null;
    }

    public function getRaw()
    {
        return $this->JSONArray;
    }

    public function get($key, $implicit = false, $default = null)
    {
        if(!$implicit)
        {
            $return = $this->JSONArray;
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
        return in_array($key, array_keys($this->JSONArray)) ? $this->JSONArray[$key] : $default;
    }

    public function getId()
    {
        return $this->get('_id', true);
    }
}

class Player extends HypixelObject {
    public function getSession()
    {
        return $this->api->getSession(array('player'=>$this->getName()));
    }

    public function getFriends()
    {
        return $this->api->getFriends(array('player'=>$this->getName()));
    }

    public function getName()
    {
        if($this->get('displayname', true) != null)
        {
            return $this->get('displayname', true);
        }
        else
        {
            $aliases = $this->get('knownAliases', true, array());
            if(sizeof($aliases) == 0)
            {
                return $this->get('playername', true);
            }
            return $aliases[0];
        }
    }

    public function getUUID(){
        return $this->get('uuid');
    }

    public function getStats()
    {
        return new Stats($this->get('stats', true, array()), $this->api);
    }

    public function isPreEULA()
    {
        return $this->get('eulaCoins', true, false);
    }

    public function getLevel()
    {
        return $this->get('networkLevel', true, 0) + 1;
    }

    public function isStaff()
    {
        $rank = $this->get('rank', true);
        if($rank == 'NORMAL' || $rank == null)
            return false;
        return true;
    }

    public function getMultiplier()
    {
        if($this->getRank(false) == 'YOUTUBER') return 7;
        $ranks = array('DEFAULT', 'VIP', 'VIP+', 'MVP', 'MVP+');
        $pre = $this->getRank(true, true);
        $flip = array_flip($ranks);
        $rankKey = $flip[$pre] + 1;
        $levelKey = floor($this->getLevel() / 25) + 1;
        return ($rankKey > $levelKey) ? $rankKey : $levelKey;
    }

    public function getRank($package = true, $preEULA = false)
    {
        if($package)
        {
            $keys = array('newPackageRank', 'packageRank');
            if($preEULA) $keys = array_reverse($keys);
            if(!$this->isStaff()) {
                if (!$this->isPreEULA())
                {
                    if($this->get($keys[0], true))
                    {
                        return str_replace('_PLUS', '+', $this->get($keys[0], true));
                    }
                }
                else
                {
                    foreach($keys as $key)
                    {
                        if($this->get($key, true))
                        {
                            return str_replace('_PLUS', '+', $this->get($key, true));
                        }
                    }
                }
            }
            else
            {
                foreach($keys as $key)
                {
                    if($this->get($key, true))
                    {
                        return str_replace('_PLUS', '+', $this->get($key, true));
                    }
                }
            }
        }
        else
        {
            $rank = $this->get('rank', true);
            if(!$this->isStaff()) return $this->getRank(true, $preEULA);
            return $rank;
        }
        return 'DEFAULT';
    }

    public function getAchievements() {

    }

    public function getAchievementPoints() {
        return 'WIP';
    }
}

class OneTimeAchievement {

}
class TieredAchievement {

}
class Tier {

}

class Stats extends HypixelObject {
    public function getGame($game)
    {
        return new GameStats(isset($this->JSONArray[$game]) ? $this->JSONArray[$game] : null, $this->api);
    }
}
class GameStats extends HypixelObject { /* Dummy for now */ }

class Session extends HypixelObject {
    public function getPlayers() {
        return $this->get('players', true);
    }

    public function getGame() {
        return $this->get('gameType', true);
    }

    public function getServer() {
        return $this->get('server', true);
    }
}

class Friends extends HypixelObject { /* Dummy for now */ }

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
        $this->members = new MemberList($this->JSONArray['members'], $this->api);
        return $this->getMemberList();
    }

    public function getMemberCount()
    {
        if($this->members == null)
            $this->members = new MemberList($this->JSONArray['members'], $this->api);
        return $this->members->getMemberCount();
    }

    public function getMaxMembers()
    {
        $upgrades = array(0,5,5,5,5,5,5,5,5,10,5,5,5,5,5,5,5,10);
        $base = 25;

        $total = 0;
        for($i = 0; $i <= $this->get('memberSizeLevel', true, 0); $i++)
        {
            @$total += $upgrades[$i];
        }

        $total += $base;
        return $total;
    }
}
class MemberList extends HypixelObject {
    private $list;
    private $count;

    public function __construct($json, $api)
    {
        $this->JSONArray = $json;
        $this->api = $api;

        $list = array("GUILDMASTER"=>array(), "OFFICER"=>array(), "MEMBER"=>array());
        $this->count = sizeof($json);
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

    public function getMemberCount()
    {
        return $this->count;
    }
}
