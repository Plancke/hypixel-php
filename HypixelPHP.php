<?php
namespace HypixelPHP;

/**
 * HypixelPHP
 *
 * @author Plancke
 * @version 1.4.0
 * @link  http://plancke.nl
 *
 */
class HypixelPHP
{
    private $options;
    const MAX_CACHE_TIME = 999999999999;

    public function  __construct($input = array())
    {
        $this->options = array_merge(
            array(
                'api_key' => '',
                'cache_time' => 600,
                'cache_folder_player' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/player',
                'cache_folder_guild' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/guild',
                'cache_folder_friends' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/friends',
                'cache_folder_sessions' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/sessions',
                'cache_boosters' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/boosters.json',
                'cache_leaderboards' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/leaderboards.json',
                'cache_keyInfo' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/keyInfo.json',
                'debug' => false,
                'use_curl' => true
            ),
            $input
        );

        if (!file_exists($this->options['cache_folder_player'])) {
            mkdir($this->options['cache_folder_player'], 0777, true);
        }
        if (!file_exists($this->options['cache_folder_guild'])) {
            mkdir($this->options['cache_folder_guild'], 0777, true);
        }
        if (!file_exists($this->options['cache_folder_friends'])) {
            mkdir($this->options['cache_folder_friends'], 0777, true);
        }
        if (!file_exists($this->options['cache_folder_sessions'])) {
            mkdir($this->options['cache_folder_sessions'], 0777, true);
        }
    }

    public function set($input)
    {
        foreach ($input as $key => $val) {
            if ($key != 'api_key' && $key != 'debug') {
                if ($this->options[$key] != $val) {
                    $this->debug('Setting ' . $key . ' to ' . $val);
                }
            }
            $this->options[$key] = $val;
        }
    }

    public function debug($message)
    {
        if ($this->options['debug']) {
            echo '<!-- ' . $message . ' -->';
        }
    }

    public function setKey($key)
    {
        $this->set(array('api_key' => $key));
    }

    public function getKey()
    {
        return $this->options['api_key'];
    }

    public function getKeyInfo()
    {
        $filename = $this->options['cache_keyInfo'];
        $content = $this->getCache($filename);
        if ($content != null) {
            $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
            if (time() - $this->getCacheTime() < $timestamp) {
                $this->debug('Getting Cached data!');
                return new KeyInfo($content, $this);
            }
        }

        $response = $this->fetch('key');
        if ($response['success'] == true) {
            $this->debug(json_encode($response));
            $content = array('timestamp' => time(), 'record' => $response['record']);
            $this->setFileContent($filename, json_encode($content));
            return new KeyInfo($content, $this);
        }
        return null;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function hasPaid($name, $url = 'https://mcapi.ca/other/haspaid/{{NAME}}')
    {
        $hasPaid = $this->getUrlContents(str_replace("{{NAME}}", $name, $url));
        if (!isset($hasPaid['premium'])) return false;
        $this->debug('Premium (' . $name . '): ' . ($hasPaid['premium'] ? 'true' : 'false'));
        return $hasPaid['premium'];
    }

    public function getUrlContents($url)
    {
        $timeout = 2;
        $errorOut = array("success" => false, 'cause' => 'Timeout');
        if ($this->options['use_curl']) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            $curlOut = curl_exec($ch);
            if ($curlOut === false) {
                $errorOut['cause'] = curl_error($ch);
                return $errorOut;
            }
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($status != '200') {
                return $errorOut;
            }
            return json_decode($curlOut, true);
        } else {
            $ctx = stream_context_create(array(
                    'https' => array(
                        'timeout' => $timeout
                    )
                )
            );
            $out = file_get_contents($url, 0, $ctx);
            if ($out === false) {
                return $errorOut;
            }
            return json_decode($out, true);
        }
    }

    public function getCacheTime()
    {
        return $this->options['cache_time'];
    }

    public function setCacheTime($cache_time = 600)
    {
        $this->set(array('cache_time' => $cache_time));
    }

    public function log($string)
    {
        file_put_contents('fetch.log', '[' . date("Y-m-d H:i:s") . '] ' . $string . "\r\n", FILE_APPEND);
    }

    public function fetch($request, $key = null, $val = null)
    {
        if ($this->getCacheTime() >= self::MAX_CACHE_TIME) {
            $return = array("success" => false, 'cause' => 'Max Cache Time!');
            return $return;
        }
        $this->log('Starting Fetch: ' . $this->getKey() . ' - ' . $request . '?' . $key . '=' . $val);
        $this->debug('Starting Fetch: ' . $request . '?' . $key . '=' . $val);
        $requestURL = 'https://api.hypixel.net/' . $request . '?key=' . $this->getKey();
        if ($key != null && $val != null) {
            $val = trim($val);
            $val = str_replace(' ', '%20', $val);
            $requestURL .= '&' . $key . '=' . $val;
        }

        $response = $this->getUrlContents($requestURL);
        if ($response['success'] == false) {
            if (!array_key_exists('cause', $response)) {
                $response['cause'] = 'Unknown';
            }
            $this->log('Fetch Failed: ' . $response['cause']);
            $this->debug('Fetch Failed: ' . $response['cause']);
        } else {
            $this->log('Fetch Successful');
            $this->debug('Fetch successful!');
        }
        return $response;
    }

    public function getPlayer($keyPair = array())
    {
        $pairs = array_merge(
            array(
                'name' => '',
                'uuid' => ''
            ),
            $keyPair
        );

        foreach ($pairs as $key => $val) {
            if ($val != null) {
                $filename = $this->options['cache_folder_player'] . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $this->getCacheFileName($val) . '.json';
                if ($key == 'uuid') {
                    $content = $this->getCache($filename);
                    if ($content != null) {
                        $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                        if (time() - $this->getCacheTime() < $timestamp) {
                            $this->debug('Getting Cached data!');
                            return $this->getPlayer(array('name' => $content['name']));
                        }
                    }

                    $response = $this->fetch('player', $key, $val);
                    if ($response['success'] == 'true') {
                        $content = array('timestamp' => time(), 'name' => $response['player']['displayname']);
                        $this->setFileContent($filename, json_encode($content));
                        return $this->getPlayer(array('name' => $content['name']));
                    }
                }

                if ($key == 'name') {
                    if (file_exists($filename) || $this->hasPaid($val)) {
                        $content = $this->getCache($filename);
                        if ($content != null) {
                            $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                            if (time() - $this->getCacheTime() < $timestamp) {
                                return new Player($content, $this);
                            }
                        }

                        $response = $this->fetch('player', $key, $val);
                        if ($response['success'] == 'true') {
                            if ($response['player'] != null) {
                                $PLAYER = new Player(array(
                                    'record' => $response['player'],
                                    'extra' => $content['extra']
                                ), $this);
                                $PLAYER->setExtra(array('filename' => $filename));
                                $this->setCache($filename, $PLAYER);
                                return $PLAYER;
                            }
                        }
                    }
                }
            }
        }
        if ($this->getCacheTime() < self::MAX_CACHE_TIME) {
            $this->setCacheTime(self::MAX_CACHE_TIME);
            return $this->getPlayer($pairs);
        }
        return null;
    }

    public function getGuild($keyPair = array())
    {
        $pairs = array_merge(
            array(
                'byPlayer' => null,
                'byName' => null,
                'id' => null
            ),
            $keyPair
        );

        foreach ($pairs as $key => $val) {
            if ($val != null) {
                if ($key == 'byPlayer' && $val != null) {
                    if (!$this->hasPaid($val)) {
                        continue;
                    }
                }
                $filename = $this->options['cache_folder_guild'] . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $this->getCacheFileName($val) . '.json';

                if ($key == 'byPlayer' || $key == 'byName') {
                    $content = $this->getCache($filename);
                    if ($content != null) {
                        $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                        if (time() - $this->getCacheTime() < $timestamp) {
                            if (isset($content['guild'])) {
                                return $this->getGuild(array('id' => $content['guild']));
                            }
                            continue;
                        }
                    }

                    $response = $this->fetch('findGuild', $key, $val);
                    if ($response['success'] == 'true') {
                        $content = array('timestamp' => time(), 'guild' => $response['guild']);
                        $this->setFileContent($filename, json_encode($content));
                        return $this->getGuild(array('id' => $response['guild']));
                    }
                }

                if ($key == 'id') {
                    $content = $this->getCache($filename);
                    if ($content != null) {
                        $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                        if (time() - $this->getCacheTime() < $timestamp) {
                            return new Guild($content, $this);
                        }
                    }

                    $response = $this->fetch('guild', $key, $val);
                    if ($response['success'] == 'true') {
                        $GUILD = new Guild(array(
                            'record' => $response['guild'],
                            'extra' => $content['extra']
                        ), $this);
                        $GUILD->setExtra(array('filename' => $filename));
                        $this->setCache($filename, $GUILD);
                        return $GUILD;
                    }
                }
            }
        }
        if ($this->getCacheTime() < self::MAX_CACHE_TIME) {
            $this->setCacheTime(self::MAX_CACHE_TIME);
            return $this->getGuild($pairs);
        }
        return null;
    }

    public function getSession($keyPair = array())
    {
        $pairs = array_merge(
            array(
                'player' => null
            ),
            $keyPair
        );

        foreach ($pairs as $key => $val) {
            if ($val != null) {
                if ($key == 'player') {

                    $filename = $this->options['cache_folder_sessions'] . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $this->getCacheFileName($val) . '.json';
                    $content = $this->getCache($filename);
                    if ($content != null) {
                        $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                        if (time() - $this->getCacheTime() < $timestamp) {
                            return new Session($content, $this);
                        }
                    }

                    $response = $this->fetch('session', $key, $val);
                    if ($response['success'] == 'true') {
                        $SESSION = new Session(array(
                            'record' => $response['session'],
                            'extra' => $content['extra']
                        ), $this);
                        $SESSION->setExtra(array('filename' => $filename));
                        $this->setCache($filename, $SESSION);
                        return $SESSION;
                    }
                }
            }
        }

        if ($this->getCacheTime() < self::MAX_CACHE_TIME) {
            $this->setCacheTime(self::MAX_CACHE_TIME);
            return $this->getSession($pairs);
        }
        return null;
    }

    public function getFriends($keyPair = array())
    {
        $pairs = array_merge(
            array(
                'player' => null
            ),
            $keyPair
        );

        foreach ($pairs as $key => $val) {
            if ($val != null) {
                if ($key == 'player') {
                    $filename = $this->options['cache_folder_friends'] . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $this->getCacheFileName($val) . '.json';
                    $content = $this->getCache($filename);
                    if ($content != null) {
                        $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                        if (time() - $this->getCacheTime() < $timestamp) {
                            return new Friends($content, $this);
                        }
                    }

                    $response = $this->fetch('friends', $key, $val);
                    if ($response['success'] == 'true') {
                        $FRIENDS = new Friends(array(
                            'record' => $response['records'],
                            'extra' => $content['extra']
                        ), $this);
                        $FRIENDS->setExtra(array('filename' => $filename));
                        $this->setCache($filename, $FRIENDS);
                        return $FRIENDS;
                    }
                }
            }
        }

        if ($this->getCacheTime() < self::MAX_CACHE_TIME) {
            $this->setCacheTime(self::MAX_CACHE_TIME);
            return $this->getFriends($pairs);
        }
        return null;
    }

    public function getBoosters()
    {
        $filename = $this->options['cache_boosters'];
        $content = $this->getCache($filename);
        if ($content != null) {
            $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
            if (time() - $this->getCacheTime() < $timestamp) {
                return new Boosters($content, $this);
            }
        }

        $response = $this->fetch('boosters');
        if ($response['success'] == 'true') {
            $BOOSTERS = new Boosters(array(
                'record' => $response['boosters'],
                'extra' => $content['extra']
            ), $this);
            $BOOSTERS->setExtra(array('filename' => $filename));
            $this->setCache($filename, $BOOSTERS);
            return $BOOSTERS;
        }

        if ($this->getCacheTime() < self::MAX_CACHE_TIME) {
            $this->setCacheTime(self::MAX_CACHE_TIME);
            return $this->getBoosters();
        }
        return null;
    }

    public function getLeaderboards()
    {
        $filename = $this->options['cache_leaderboards'];
        $content = $this->getCache($filename);
        if ($content != null) {
            $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
            if (time() - $this->getCacheTime() < $timestamp) {
                return new Leaderboards($content, $this);
            }
        }

        $response = $this->fetch('leaderboards');
        if ($response['success'] == 'true') {
            $LEADERBOARDS = new Leaderboards(array(
                'record' => $response['leaderboards'],
                'extra' => $content['extra']
            ), $this);
            $LEADERBOARDS->setExtra(array('filename' => $filename));
            $this->setCache($filename, $LEADERBOARDS);
            return $LEADERBOARDS;
        }

        if ($this->getCacheTime() < self::MAX_CACHE_TIME) {
            $this->setCacheTime(self::MAX_CACHE_TIME);
            return $this->getLeaderboards();
        }
        return null;
    }

    public function getFileContent($filename)
    {
        $content = null;
        if (file_exists($filename)) {
            if (!file_exists(dirname($filename))) {
                @mkdir(dirname($filename), 0777, true);
            }
            $this->debug('Getting contents of ' . $filename);
            $file = fopen($filename, 'r+');
            if (filesize($filename) > 0) {
                $content = fread($file, filesize($filename));
            }
            fclose($file);
        }
        return $content;
    }

    public function setFileContent($filename, $content)
    {
        $this->debug('Setting contents of ' . $filename);
        if (!file_exists(dirname($filename))) {
            @mkdir(dirname($filename), 0777, true);
        }
        $file = fopen($filename, 'w+');
        fwrite($file, $content);
        fclose($file);
    }

    public function getCacheFileName($input)
    {
        $input = strtolower($input);
        $input = trim($input);
        $input = str_replace(' ', '%20', $input);
        if (strlen($input) < 3) {
            return implode(DIRECTORY_SEPARATOR, str_split($input, 1));
        }
        return substr($input, 0, 1) . DIRECTORY_SEPARATOR . substr($input, 1, 1) . DIRECTORY_SEPARATOR . substr($input, 2);
    }

    public function getCache($filename)
    {
        $content = $this->getFileContent($filename);
        if ($content == null) {
            return null;
        }
        $content = json_decode($content, true);
        if (!array_key_exists('extra', $content)) {
            $content['extra'] = array();
        }
        return $content;
    }

    public function setCache($filename, HypixelObject $obj)
    {
        $content = json_encode($obj->getRaw());
        $this->setFileContent($filename, $content);
    }

    public function getRanks()
    {
        $ranks = array(
            'ADMIN' => array(
                'prefix' => 'ADMIN',
                'colors' => array(
                    'front' => 'FF5555',
                    'back' => '3F1515'
                )
            ),
            'JR DEV' => array(
                'prefix' => 'JR DEV',
                'colors' => array(
                    'front' => '55FF55',
                    'back' => '153F15'
                )
            ),
            'MODERATOR' => array(
                'prefix' => 'MOD',
                'colors' => array(
                    'front' => '00AA00',
                    'back' => '002A00'
                )
            ),
            'HELPER' => array(
                'prefix' => 'HELPER',
                'colors' => array(
                    'front' => '0000AA',
                    'back' => '00002A'
                )
            ),
            'JR HELPER' => array(
                'prefix' => 'JR HELPER',
                'colors' => array(
                    'front' => '0000AA',
                    'back' => '00002A'
                )
            ),
            'YOUTUBER' => array(
                'prefix' => 'YT',
                'colors' => array(
                    'front' => 'FFAA00',
                    'back' => '2A2A00'
                )
            ),
            'MVP+' => array(
                'prefix' => 'MVP+',
                'colors' => array(
                    'front' => '22CCCC',
                    'back' => '153F3F',
                    'plus' => 'FF5555'
                )
            ),
            'MVP' => array(
                'prefix' => 'MVP',
                'colors' => array(
                    'front' => '22CCCC',
                    'back' => '153F3F'
                )
            ),
            'VIP+' => array(
                'prefix' => 'VIP+',
                'colors' => array(
                    'front' => '22CC22',
                    'back' => '153F15',
                    'plus' => 'FFAA00'
                )
            ),
            'VIP' => array(
                'prefix' => 'VIP',
                'colors' => array(
                    'front' => '22CC22',
                    'back' => '153F15'
                )
            ),
            'DEFAULT' => array(
                'colors' => array(
                    'front' => 'AAAAAA',
                    'back' => 'A2A2A2'
                )
            ),
            'NONE' => array(
                'prefix' => 'NONE',
                'colors' => array(
                    'front' => 'AAAAAA',
                    'back' => 'A2A2A2'
                )
            )
        );

        return $ranks;
    }

    public function getRankInfo($rank = 'NONE')
    {
        $rankInfo = $this->getRanks();
        if (!array_key_exists($rank, $rankInfo)) {
            $rank = 'NONE';
        }
        return $rankInfo[$rank];
    }
}

class HypixelObject
{
    public $JSONArray;
    public $api;

    public function __construct($json, HypixelPHP $api)
    {
        $this->JSONArray = $json;
        $this->api = $api;
        if ($this->JSONArray == null) {
            $this->JSONArray = array();
        }
        if (!array_key_exists('extra', $this->JSONArray) || $this->JSONArray['extra'] == null) {
            $this->JSONArray['extra'] = array();
        }
        if (!array_key_exists('timestamp', $this->JSONArray)) {
            $this->JSONArray['timestamp'] = time();
        }
    }

    /** @deprecated */
    public function isNull()
    {
        return !array_key_exists('record', $this->JSONArray);
    }

    public function getRaw()
    {
        return $this->JSONArray;
    }

    public function get($key, $implicit = false, $default = null)
    {
        if (!array_key_exists('record', $this->JSONArray)) return $default;
        $record = $this->JSONArray['record'];
        if (!is_array($record)) return $default;
        if (!$implicit) {
            $return = $record;
            foreach (explode(".", $key) as $split) {
                $return = isset($return[$split]) ? $return[$split] : $default;
            }
            return $return ? $return : $default;
        }
        return in_array($key, array_keys($record)) ? $record[$key] : $default;
    }

    public function getId()
    {
        return $this->get('_id', true);
    }

    public function isCached()
    {
        return $this->getCacheTime() > 0;
    }

    public function getCacheTime()
    {
        return $this->JSONArray['timestamp'];
    }

    public function setExtra($input)
    {
        $anyChange = false;
        foreach ($input as $key => $val) {
            if ($val == null) {
                unset($this->JSONArray['extra'][$key]);
                $anyChange = true;
                continue;
            }
            if (array_key_exists($key, $this->JSONArray['extra'])) {
                if ($this->JSONArray['extra'][$key] == $val) {
                    $anyChange = true;
                    continue;
                }
            }
            $this->api->debug('Extra \'' . $key . '\' set to ' . $val);
            $this->JSONArray['extra'][$key] = $val;
            $anyChange = true;
        }
        if ($anyChange) {
            $this->saveCache();
        }
    }

    public function getExtra()
    {
        return $this->JSONArray['extra'];
    }

    public function saveCache()
    {
        if (array_key_exists('filename', $this->getExtra())) {
            $this->api->debug('Saving cache file');
            $this->api->setCache($this->JSONArray['extra']['filename'], $this);
        }
    }
}

class KeyInfo extends HypixelObject
{

}

class Player extends HypixelObject
{
    public function getSession()
    {
        return $this->api->getSession(array('player' => $this->getName()));
    }

    public function getFriends()
    {
        return $this->api->getFriends(array('player' => $this->getName()));
    }

    public function getName()
    {
        if ($this->get('displayname', true) != null) {
            return $this->get('displayname', true);
        } else {
            $aliases = $this->get('knownAliases', true, array());
            if (sizeof($aliases) == 0) {
                return $this->get('playername', true);
            }
            return $aliases[0];
        }
    }

    public function getFormattedPrefix($rankOptions = array(false, false))
    {
        $playerRank = $this->getRank($rankOptions[0], $rankOptions[1]);
        $rankInfo = $this->api->getRankInfo($playerRank);
        $rankPrefix = $this->getPrefix();
        if ($rankPrefix != null) {
            /*
            if (array_key_exists('prefix', $rankInfo)) {
                return '<span style="color: #' . $rankInfo['colors']['front'] . ';' . $extraCSS . '">' . $prefix . $this->getName() . '</span>';
            }*/
        }
        $prefix = '';
        if (array_key_exists('prefix', $rankInfo)) {
            $prefix = '[' . $rankInfo['prefix'] . '] ';
            if (array_key_exists('plus', $rankInfo['colors'])) {
                $prefix = str_replace('+', '<span style="color: #' . $rankInfo['colors']['plus'] . ';">+</span>', $prefix);
            }
        }
        return $prefix;
    }

    public function getPrefixedName($rankOptions = array(false, false), $extraCSS = '')
    {
        $playerRank = $this->getRank($rankOptions[0], $rankOptions[1]);
        $rankInfo = $this->api->getRankInfo($playerRank);
        return '<span style="color: #' . $rankInfo['colors']['front'] . ';' . $extraCSS . '">' . $this->getFormattedPrefix($rankOptions) . $this->getName() . '</span>';
    }

    public function getColoredName($rankOptions = array(false, false), $extraCSS = '')
    {
        $playerRank = $this->getRank($rankOptions[0], $rankOptions[1]);
        $rankInfo = $this->api->getRankInfo($playerRank);
        return '<span style="color: #' . $rankInfo['colors']['front'] . ';' . $extraCSS . '">' . $this->getName() . '</span>';
    }

    public function getUUID()
    {
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

    public function getPrefix()
    {
        return $this->get('prefix', true);
    }

    public function isStaff()
    {
        $rank = $this->get('rank', true, 'NORMAL');
        if ($rank == 'NORMAL')
            return false;
        return true;
    }

    public function getMultiplier()
    {
        if ($this->getRank(false) == 'YOUTUBER') return 7;
        $ranks = array('DEFAULT', 'VIP', 'VIP+', 'MVP', 'MVP+');
        $pre = $this->getRank(true, true);
        $flip = array_flip($ranks);
        $rankKey = $flip[$pre] + 1;
        $levelKey = floor($this->getLevel() / 25) + 1;
        return ($rankKey > $levelKey) ? $rankKey : $levelKey;
    }

    public function getRank($package = true, $preEULA = false)
    {
        $return = 'DEFAULT';
        if ($package) {
            $keys = array('newPackageRank', 'packageRank');
            if ($preEULA) $keys = array_reverse($keys);
            if (!$this->isStaff()) {
                if (!$this->isPreEULA()) {
                    if ($this->get($keys[0], true)) {
                        $return = $this->get($keys[0], true);
                    }
                } else {
                    foreach ($keys as $key) {
                        if ($this->get($key, true)) {
                            $return = $this->get($key, true);
                            break;
                        }
                    }
                }
            } else {
                foreach ($keys as $key) {
                    if ($this->get($key, true)) {
                        $return = $this->get($key, true);
                        break;
                    }
                }
            }
        } else {
            $rank = $this->get('rank', true);
            if (!$this->isStaff()) return $this->getRank(true, $preEULA);
            $return = $rank;
        }
        if ($return == 'NONE' && $preEULA) {
            return $this->getRank($package, !$preEULA);
        }
        return str_replace('_', ' ', str_replace('_PLUS', '+', $return));
    }

    public function getAchievementPoints()
    {
        return 'WIP';
    }

    public function get($key, $implicit = false, $default = null)
    {
        if ($key == 'achievementPoints') {
            return $this->getAchievementPoints();
        }
        return parent::get($key, $implicit, $default);
    }
}

class Achievements extends HypixelObject
{
    const GAME_GENERAL = "general";
    const GAME_QUAKE = "quake";
    const GAME_PAINTBALL = "paintball";
    const GAME_WALLS = "walls";
    const GAME_VAMPIREZ = "vampirez";
    const GAME_BLITZ = "blitz";
    const GAME_MEGAWALLS = "walls3";
    const GAME_ARENA = "arena";

    const GAME_ARCADE = "arcade";
    const GAME_ARCADE_CREEPERATTACK = "arcade.creeper";

    const GAME_TNTGAMES = "tntgames";
    const GAME_TNTGAMES_WIZARDS = "tntgames.wizards";
    const GAME_TNTGAMES_BOW = "tntgames.bow";
    const GAME_TNTGAMES_TNT_RUN = "tntgames.tnt.run";
    const GAME_TNTGAMES_TNT_TAG = "tntgames.tnt.tag";


    private $player;

    public function __construct($json, $api, $player)
    {
        parent::__construct($json, $api);
        $this->player = $player;
    }

    public function getByGame($key/* should be the game name */)
    {
        $achievements = $this->JSONArray;
        $availableGamesList = array();
        foreach ($achievements as $achievement) {
            $explode = explode('_', $achievement);
            $gameName = $explode[0];
            if (!in_array($gameName, array_keys($availableGamesList))) {
                $availableGamesList[$gameName] = array();
            }
            if (in_array($gameName, array("arcade", "tntgames"))) {
                $subGameName = $explode[1];
                if (!in_array($subGameName, array_keys($availableGamesList[$gameName]))) $availableGamesList[$gameName][$subGameName] = array();
                $availableGamesList[$gameName][$subGameName][] = $achievement;
            } else {
                $availableGamesList[$gameName][] = $achievement;
            }
        }
        if (count($availableGamesList) <= 0)
            return null;
        $tmpGameList = $availableGamesList;
        foreach (explode('.', $key) as $subGame) {
            if (in_array($subGame, array_keys($tmpGameList))) $tmpGameList = $tmpGameList[$subGame];
            else return null;
        }
        $ret = array();
        foreach ($tmpGameList as $game => $achievement) {
            $ret[] = new OneTimeAchievement($achievement, $this->player);
        }
        return $ret;
    }

    public function hasAchievement($ach)
    {
        return in_array($ach, $this->JSONArray);
    }
}

class OneTimeAchievement
{

    private $name;
    private $player;

    public function __construct($key = null, $player = null)
    {
        $this->name = $key;
        $this->player = $player;
    }

    public function isNull()
    {
        return $this->name == null || !is_string($this);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        // not done for now
    }

    public function __toString()
    {
        if ($this->isNull()) {
            return "";
        }
        return $this->name;
    }
}

class TieredAchievement
{
}

class Tier
{
}

class Stats extends HypixelObject
{
    public function __construct($json, HypixelPHP $api)
    {
        parent::__construct(array('record' => $json), $api);
    }

    public function getGame($game)
    {
        $game = $this->get($game, true, null);
        return new GameStats($game, $this->api);
    }
}

class GameStats extends HypixelObject
{
    public function __construct($json, HypixelPHP $api)
    {
        parent::__construct(array('record' => $json), $api);
    }

    public function getPackages()
    {
        return $this->get('packages', false, array());
    }
}

class Session extends HypixelObject
{
    public function getPlayers()
    {
        return $this->get('players', true);
    }

    public function getGame()
    {
        return $this->get('gameType', true);
    }

    public function getServer()
    {
        return $this->get('server', true);
    }
}

class Friends extends HypixelObject
{
    public function __construct($json, HypixelPHP $api)
    {
        parent::__construct(array('record' => $json), $api);
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
        if ($this->members == null)
            $this->members = new MemberList($this->get('members'), $this->api);
        return $this->members;
    }

    public function getMaxMembers()
    {
        $total = 25;
        $level = $this->get('memberSizeLevel', true, -1);
        if ($level >= 0) {
            $total += 5 * $level;
        }
        return $total;
    }

    public function getMemberCount()
    {
        return $this->getMemberList()->getMemberCount();
    }
}

class MemberList extends HypixelObject
{
    private $list;
    private $count;

    public function __construct($json, $api)
    {
        parent::__construct(array('record' => $json), $api);

        $list = array("GUILDMASTER" => array(), "OFFICER" => array(), "MEMBER" => array());
        $this->count = sizeof($json);
        foreach ($json as $player) {
            $rank = $player['rank'];
            if (!in_array($rank, array_keys($list))) {
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

class GameTypes
{
    const QUAKE = 2;
    const WALLS = 3;
    const PAINTBALL = 4;
    const BSG = 5;
    const TNTGAMES = 6;
    const VAMPIREZ = 7;
    const MEGAWALLS = 13;
    const ARCADE = 14;
    const ARENA = 17;
    const UHC = 20;
    const MCGO = 21;

    public static function fromID($id)
    {
        switch ($id) {
            case 2:
                return new GameType('quake', 'Quake', 'Quake', 2);
                break;
            case 3:
                return new GameType('walls', 'Walls', 'Walls', 3);
                break;
            case 4:
                return new GameType('paintball', 'Paintball', 'PB', 4);
                break;
            case 5:
                return new GameType('hungergames', 'Blitz Survival Games', 'BSG', 5);
                break;
            case 6:
                return new GameType('tntgames', 'TNT Games', 'TNT', 6);
                break;
            case 7:
                return new GameType('vampirez', 'VampireZ', 'VampZ', 7);
                break;
            case 13:
                return new GameType('walls3', 'MegaWalls', 'MW', 13);
                break;
            case 14:
                return new GameType('arcade', 'Arcade', 'Arcade', 14);
                break;
            case 17:
                return new GameType('arena', 'Arena', 'Arena', 17);
                break;
            case 20:
                return new GameType('uhc', 'UHC Champions', 'UHC', 20);
                break;
            case 21:
                return new GameType('mcgo', 'Cops and Crims', 'CaC', 21);
                break;
        }
        return null;
    }

    public static function getAllTypes()
    {
        $obj = new \ReflectionClass ('\HypixelPHP\GameTypes');
        return $obj->getConstants();
    }
}

class GameType
{
    private $db, $name, $short, $id;

    public function __construct($db, $name, $short, $id)
    {
        $this->db = $db;
        $this->name = $name;
        $this->short = $short;
        $this->id = $id;
    }

    public function getDb()
    {
        return $this->db;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getShort()
    {
        return $this->short;
    }

    public function getId()
    {
        return $this->id;
    }
}

class Boosters extends HypixelObject
{
    public function getQueue($gameType = GameTypes::ARCADE, $max = 10)
    {
        $return = array(
            'boosters' => array(),
            'total' => 0
        );
        foreach ($this->JSONArray['record'] as $boosterInfo) {
            $booster = new Booster($boosterInfo, $this->api);
            if ($booster->getGameTypeID() == $gameType) {
                if ($return['total'] < $max) {
                    array_push($return['boosters'], $booster);
                }
                $return['total']++;
            }
        }
        return $return;
    }

    public function getBoosters($playerName)
    {
        $boosters = array();
        foreach ($this->JSONArray['record'] as $boosterInfo) {
            $booster = new Booster($boosterInfo, $this->api);
            if (strtolower($booster->getOwner()) == strtolower($playerName)) {
                array_push($boosters, $booster);
            }
        }
        return $boosters;
    }
}

class Booster
{
    private $info;
    private $api;

    public function __construct($info, HypixelPHP $api)
    {
        $this->info = $info;
        $this->api = $api;
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        if (isset($this->info['purchaser'])) {
            return $this->info['purchaser'];
        }
        $oldTime = $this->api->getCacheTime();
        $this->api->setCacheTime(HypixelPHP::MAX_CACHE_TIME - 1);
        $player = $this->api->getPlayer(array('uuid' => $this->info['purchaserUuid']));
        $this->api->setCacheTime($oldTime);
        if ($player != null) {
            /** @var $player Player */
            return $player->getName();
        }
        return null;
    }

    /**
     * @return int
     */
    public function getGameTypeID()
    {
        return $this->info['gameType'];
    }

    /**
     * @return GameType|null
     */
    public function getGameType()
    {
        return GameTypes::fromID($this->info['gameType']);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getLength() != $this->getLength(true);
    }

    /**
     * @param bool $original
     * @return int
     */
    public function getLength($original = false)
    {
        if ($original)
            return $this->info['originalLength'];
        return $this->info['length'];
    }

    public function getActivateTime()
    {
        return $this->info['dateActivated'];
    }
}

class Leaderboards extends HypixelObject
{
    public function __construct($json, HypixelPHP $api)
    {
        parent::__construct(array('record' => $json), $api);
    }
}