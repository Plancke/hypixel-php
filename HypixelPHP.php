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

    /**
     * @param array $input
     */
    public function  __construct($input = array())
    {
        $this->options = array_merge(
            array(
                'api_key'               => '', // Your Hypixel API-key
                'cache_time'            => 600, // Time to cache statistics, in seconds
                'cache_uuid_time'       => 864000, // Time to cache UUIDs for playernames, in seconds. Playernames don't change often, so why cache them not a little longer?
                'timeout' => 2, // Timeout to wait for connecting and waiting on the API server and other sites, in seconds. Longer may be more stable, but also more annoying to end-users.
                'cache_folder_player'   => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/player', // Cache folder for playerdata
                'cache_folder_guild'    => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/guild', // Cache folder for guild data
                'cache_folder_friends'  => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/friends', // Cache folder for friend data
                'cache_folder_sessions' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/sessions', // Cache folder for session data
                'cache_folder_uuids'    => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/uuids', // Cache folder to store playernames and their uuids in.
                'cache_boosters'        => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/boosters.json', // Cache file for booster data
                'cache_leaderboards'    => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/leaderboards.json', // Cache file for leaderboards
                'cache_keyInfo'         => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/keyInfo.json', // Cache file for storing keyInfo
                'achievements_file' => $_SERVER['DOCUMENT_ROOT'] . '/hypixel/assets/achievements.json',
                'log_folder' => $_SERVER['DOCUMENT_ROOT'] . '/logs/HypixelAPI',
                'logging' => true,
                'debug' => true,
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
        if (!file_exists($this->options['cache_folder_uuids'])) {
            mkdir($this->options['cache_folder_uuids'], 0777, true);
        }
        if (!file_exists($this->options['log_folder'])) {
            mkdir($this->options['log_folder'], 0777, true);
        }
    }

    /**
     * @param $input
     */
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

    /**
     * @param $message
     */
    public function debug($message)
    {
        if ($this->options['debug']) {
            echo '<!-- ' . $message . ' -->';
        }
        if ($this->options['logging']) {
            $this->log($message);
        }
    }

    /**
     * @param $key
     */
    public function setKey($key)
    {
        $this->set(array('api_key' => $key));
    }

    public function getKey()
    {
        return $this->options['api_key'];
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Checks if $name is a paid account
     * @param $name
     * @param string $url
     * @return bool
     */
    public function hasPaid($name, $url = 'https://mcapi.ca/other/haspaid/{{NAME}}')
    {
        $hasPaid = $this->getUrlContents(str_replace("{{NAME}}", $name, $url));
        if (!isset($hasPaid['premium'])) return false;
        $this->debug('Premium (' . $name . '): ' . ($hasPaid['premium'] ? 'true' : 'false'));
        return $hasPaid['premium'];
    }

    /**
     * @param $url
     * @param int $timeout
     * @return array|mixed json decoded array of response or error json
     */
    public function getUrlContents($url, $timeout = 2)
    {
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

    /**
     * Returns the currently set cache threshold
     * @param null $for
     * @return int
     */
    public function getCacheTime($for = null)
    {
        if ($for === null) return $this->options['cache_time'];
        if ($for === 'uuid') return $this->options['cache_uuid_time'];
        return $this->options['cache_time'];
    }

    /**
     * @param int  $cache_time
     * @param null $for
     */
    public function setCacheTime($cache_time = 600, $for = null)
    {
        if ($for === null) $this->set(['cache_time' => $cache_time]);
        if ($for === 'uuid') $this->set(['cache_uuid_time' => $cache_time]);
        $this->set(['cache_time' => $cache_time]);
    }

    /**
     * Log $string to log files
     * Directory setup:
     *  - LOG_FOLDER/DATE/1.log
     *  - LOG_FOLDER/DATE/2.log
     * separated every 25MB
     * @param $string
     */
    public function log($string)
    {
        $dirName = $this->options['log_folder'] . DIRECTORY_SEPARATOR . date("Y-m-d");
        if (!file_exists($dirName)) {
            mkdir($dirName, 0777, true);
        }
        $scanDir = array_diff(scandir($dirName), array('.', '..'));
        $numberOfLogs = sizeof($scanDir);
        if ($numberOfLogs == 0) {
            $numberOfLogs++;
        }
        $filename = $dirName . DIRECTORY_SEPARATOR . $numberOfLogs . '.log';
        if (file_exists($filename)) {
            if (filesize($filename) > 25600000) {
                $filename = $dirName . DIRECTORY_SEPARATOR . ($numberOfLogs + 1) . '.log';
            }
        }
        file_put_contents($filename, '[' . date("H:i:s") . '] ' . $string . "\r\n", FILE_APPEND);
    }

    /**
     * @param      $request
     * @param null $key
     * @param null $val
     * @param      $timeout
     *
     * @return array|mixed
     */
    public function fetch($request, $key = null, $val = null, $timeout = -1)
    {
        if ($this->getCacheTime() >= self::MAX_CACHE_TIME) {
            $return = array("success" => false, 'cause' => 'Max Cache Time!');
            return $return;
        }

        if ($timeout < 0) {
            $timeout = $this->options['timeout'];
        }

        $requestURL = 'https://api.hypixel.net/' . $request . '?key=' . $this->getKey();
        $debug = $request;
        if ($key != null && $val != null) {
            $val = trim($val);
            $val = str_replace(' ', '%20', $val);
            $requestURL .= '&' . $key . '=' . $val;
            $debug .= '?' . $key . '=' . $val;
        }
        $this->debug('Starting Fetch: ' . $debug);

        $response = $this->getUrlContents($requestURL, $timeout);
        if ($response['success'] == false) {
            if (!array_key_exists('cause', $response)) {
                $response['cause'] = 'Unknown';
            }
            $this->debug('Fetch Failed: ' . $response['cause']);
        } else {
            $this->debug('Fetch successful!');
        }
        return $response;
    }

    /**
     * @param array $keyPair
     * @return Player|null
     */
    public function getPlayer($keyPair = array())
    {
        $pairs = array_merge(
            array(
                'name' => null,
                'uuid' => null
            ),
            $keyPair
        );

        foreach ($pairs as $key => $val) {
            if ($val != null && $val != '') {
                $filename = $this->options['cache_folder_player'] . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $this->getCacheFileName($val) . '.json';
                if ($key == 'uuid') {
                    $content = $this->getCache($filename);
                    if ($content != null) {
                        $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                        if (time() - $this->getCacheTime() < $timestamp) {
                            return new Player($content, $this);
                        }
                    }

                    $response = $this->fetch('player', $key, $val);
                    if ($response['success'] == 'true') {
                        $PLAYER = new Player(array(
                            'record' => $response['player'],
                            'extra' => $content['extra']
                        ), $this);
                        $PLAYER->setExtra(array('filename' => $filename));
                        $this->setCache($filename, $PLAYER);
                        return $PLAYER;
                    }
                } else if ($key == 'name') {
                    if (file_exists($filename) || $this->hasPaid($val)) {
                        $content = $this->getCache($filename);
                        if ($content != null) {
                            $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                            if (time() - $this->getCacheTime() < $timestamp) {
                                if (array_key_exists("uuid", $content)) {
                                    return $this->getPlayer(array('uuid' => $content['uuid']));
                                }
                            }
                        }

                        $response = $this->getUrlContents('https://api.mojang.com/users/profiles/minecraft/' . $val);
                        if (isset($response['name']) && isset($response['id'])) {
                            $content = array(
                                'timestamp' => time(),
                                'name' => $response['name'],
                                'uuid' => $response['id']
                            );
                            $this->setFileContent($filename, json_encode($content));
                            return $this->getPlayer(array('uuid' => $content['uuid']));
                        }
                    }
                } else if($key == 'unknown') {
                    $this->debug('Determining type.');
                    $type = $this->getType($val);
                    if ($type == 'username') {
                        $this->debug('Input is username, fetching UUID.');
                        $uuid = $this->getUUID($val);
                    } else {
                        $this->debug('Input is already UUID.');
                        $uuid = $val;
                    }
                    return $this->getPlayer(['uuid' => $uuid]);
                }
            }
        }
        if ($this->getCacheTime() < self::MAX_CACHE_TIME) {
            $this->setCacheTime(self::MAX_CACHE_TIME);
            return $this->getPlayer($pairs);
        }
        return null;
    }

    /**
     * get Guild of Player
     * @param array $keyPair
     * @return Guild|null
     */
    public function getGuild($keyPair = array())
    {
        $pairs = array_merge(
            array(
                'player' => null,
                'byUuid' => null,
                'byPlayer' => null,
                'byName' => null,
                'id' => null
            ),
            $keyPair
        );

        foreach ($pairs as $key => $val) {
            if ($val != null && $val != '') {
                if ($key == 'player') {
                    /* @var $val Player */
                    return $this->getGuild(array('byPlayer' => $val->getName()));
                    // return $this->getGuild(array('byUuid' => $val->getUUID());
                }

                $filename = $this->options['cache_folder_guild'] . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $this->getCacheFileName($val) . '.json';

                if ($key == 'byPlayer' || $key == 'byName' || $key == 'byUuid') {
                    if ($key == 'byPlayer') {
                        if (!file_exists($filename) && !$this->hasPaid($val)) {
                            $this->debug('File does not exist and ' . $val . ' is not premium!');
                            continue;
                        }
                    }

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

                    $response = $this->fetch('findGuild', $key, $val, 5);
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

    /**
     * Get Session of Player
     * @param array $keyPair
     * @return Session|null
     */
    public function getSession($keyPair = array())
    {
        $pairs = array_merge(
            array(
                'player' => null
            ),
            $keyPair
        );

        foreach ($pairs as $key => $val) {
            if ($val != null && $val != '') {
                if ($key == 'player') {
                    /* @var $val Player */
                    $filename = $this->options['cache_folder_sessions'] . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $this->getCacheFileName($val->getName()) . '.json';
                    $content = $this->getCache($filename);
                    if ($content != null) {
                        $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                        if (time() - $this->getCacheTime() < $timestamp) {
                            return new Session($content, $this);
                        }
                    }

                    $response = $this->fetch('session', $key, $val->getName());
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

    /**
     * get Friends of Player
     * @param array $keyPair
     * @return Friends|null
     */
    public function getFriends($keyPair = array())
    {
        $pairs = array_merge(
            array(
                'player' => null
            ),
            $keyPair
        );

        foreach ($pairs as $key => $val) {
            if ($val != null && $val != '') {
                if ($key == 'player') {
                    /* @var $val Player */
                    $filename = $this->options['cache_folder_friends'] . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $this->getCacheFileName($val->getName()) . '.json';
                    $content = $this->getCache($filename);
                    if ($content != null) {
                        $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                        if (time() - $this->getCacheTime() < $timestamp) {
                            return new Friends($content, $this);
                        }
                    }

                    $response = $this->fetch('friends', $key, $val->getName());
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

    /**
     * get boosters
     * @return Boosters|null
     */
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

    /**
     * get Leaderboards, Hypixel Format
     * @return Leaderboards|null
     */
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

    /**
     * get info about currently set API Key
     * @return KeyInfo|null
     */
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
            $content = array('timestamp' => time(), 'record' => $response['record']);
            $this->setFileContent($filename, json_encode($content));
            return new KeyInfo($content, $this);
        }

        if ($this->getCacheTime() < self::MAX_CACHE_TIME) {
            $this->setCacheTime(self::MAX_CACHE_TIME);
            return $this->getKeyInfo();
        }
        return null;
    }

    /**
     * @param $filename
     *
     * @return null|string
     */
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

    /**
     * @param $filename
     * @param $content
     */
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

    /**
     * @param $input
     *
     * @return string
     */
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

    /**
     * @param $filename
     *
     * @return mixed|null|string
     */
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

    /**
     * @param               $filename
     * @param HypixelObject $obj
     */
    public function setCache($filename, HypixelObject $obj)
    {
        $content = json_encode($obj->getRaw());
        $this->setFileContent($filename, $content);
    }

    /**
     * Parses MC encoded colors to HTML
     * @param $string
     * @return string
     */
    public function parseColors($string)
    {
        $MCColors = array(
            "0" => "#000000",
            "1" => "#0000AA",
            "2" => "#008000",
            "3" => "#00AAAA",
            "4" => "#AA0000",
            "5" => "#AA00AA",
            "6" => "#FFAA00",
            "7" => "#AAAAAA",
            "8" => "#555555",
            "9" => "#5555FF",
            "a" => "#55FF55",
            "b" => "#55FFFF",
            "c" => "#FF5555",
            "d" => "#FF55FF",
            "e" => "#FFFF55",
            "f" => "#FFFFFF"
        );

        if (strpos($string, "§") == -1) {
            return $string;
        }
        $d = explode("§", $string);
        $out = '';
        foreach ($d as $part) {
            $out = $out . "<span style='color:" . $MCColors[substr($part, 0, 1)] . "'>" . substr($part, 1) . "</span>";
        }
        return $out;
    }

    /**
     * Determine if the $input is a playername or UUID.
     * UUIDs have a length of 32 chars, and usernames a max. length of 16 chars.
     * @param string $input
     *
     * @return string
     */
    public static function getType($input)
    {
        if (strlen($input) === 32) return 'uuid';
        return 'username';
    }

    /**
     * @param        $username
     * @param string $url
     *
     * @return string|bool
     */
    public function getUUID($username, $url = 'https://api.mojang.com/users/profiles/minecraft/%s')
    {
        $uuidURL  = sprintf($url, $username); // sprintf may be faster than str_replace
        $filename = $this->options['cache_folder_uuids'] . DIRECTORY_SEPARATOR . $this->getCacheFileName($username) . '.json';

        $content = $this->getCache($filename);
        if ($content != null) {
            $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
            if (time() - $this->getCacheTime('uuid') < $timestamp) {
                $this->debug('UUID for username still in cache.');
                $this->debug($username . ' => ' . $content['id']);
                if (isset($content['id'])) return $content['id'];
                $this->debug('UUID was not found in cached file.');
            } else {
                $this->debug(time() - $this->getCacheTime('uuid'));
                $this->debug($timestamp);
            }
        }

        $response = $this->getUrlContents($uuidURL);
        if (isset($response['id'])) {
            $this->debug('UUID for username fetched!');
            $response['timestamp'] = time();
            $this->setFileContent($filename, json_encode($response));
            $this->debug($username . ' => ' . $response['id']);
            return $response['id'];
        }

        if ($this->getCacheTime('uuid') < self::MAX_CACHE_TIME) {
            $this->setCacheTime(self::MAX_CACHE_TIME, 'uuid');
            return $this->getUUID($username, $url);
        }
        return false;
    }


}

/**
 * Class HypixelObject
 *
 * @package HypixelPHP
 */
class HypixelObject
{
    public $JSONArray;
    public $api;

    /**
     * @param            $json
     * @param HypixelPHP $api
     */
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

    /**
     * @return array
     */
    public function getRaw()
    {
        return $this->JSONArray;
    }

    public function getRecord()
    {
        return $this->JSONArray['record'];
    }

    /**
     * @param      $key
     * @param bool $implicit
     * @param null $default
     *
     * @return array|null
     */
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

    /**
     * @return array|null
     */
    public function getId()
    {
        return $this->get('_id', true);
    }

    /**
     * @return bool
     */
    public function isCached()
    {
        return $this->getCachedTime() > 0;
    }

    /**
     * @return bool
     */
    public function isCacheExpired()
    {
        return time() - $this->api->getCacheTime() > $this->getCachedTime();
    }

    public function getCachedTime()
    {
        return $this->JSONArray['timestamp'];
    }

    /**
     * @param $input
     */
    public function setExtra($input)
    {
        $anyChange = false;
        foreach ($input as $key => $val) {
            if (array_key_exists($key, $this->JSONArray['extra'])) {
                if ($val == null) {
                    unset($this->JSONArray['extra'][$key]);
                    $anyChange = true;
                    continue;
                } else if ($this->JSONArray['extra'][$key] == $val) {
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

/**
 * Class KeyInfo
 *
 * @package HypixelPHP
 */
class KeyInfo extends HypixelObject
{

}

/**
 * Class Player
 *
 * @package HypixelPHP
 */
class Player extends HypixelObject
{
    /**
     * get Session of Player
     * @return Session|null
     */
    public function getSession()
    {
        return $this->api->getSession(array('player' => $this->getName()));
    }

    /**
     * get Friends of Player
     * @return Friends|null
     */
    public function getFriends()
    {
        return $this->api->getFriends(array('player' => $this->getName()));
    }

    /**
     * get Guild of Player
     * @return Guild|null
     */
    public function getGuild()
    {
        return $this->api->getGuild(array('player' => $this));
    }

    /**
     * @return array|float|int|mixed|null
     */
    public function getName()
    {
        if ($this->get('displayname', true) != null) {
            return $this->get('displayname', true);
        } else {
            $aliases = $this->get('knownAliases', true, array());
            if (sizeof($aliases) == 0) {
                return $this->get('playername', true);
            }
            return end($aliases);
        }
    }

    /**
     * get Colored name of Player, with prefix or not
     * @param array $rankOptions
     * @param bool $prefix
     * @return string
     */
    public function getFormattedName($rankOptions = array(false, false), $prefix = true)
    {
        $playerRank = $this->getRank($rankOptions[0], $rankOptions[1]);
        $rankInfo = $this->getRankInfo($playerRank);
        if ($prefix) {
            $rankPrefix = $this->getPrefix();
            if ($rankPrefix != null) {
                $out = $rankPrefix . ' ' . $this->getName();
            } else {
                $out = $rankInfo['prefix'] . ' ' . $this->getName();
            }
        } else {
            $out = $rankInfo['color'] . $this->getName();
        }
        return $this->api->parseColors($out);
    }

    /**
     * @return array|float|int|mixed|null
     */
    public function getUUID()
    {
        return $this->get('uuid');
    }

    /**
     * @return Stats
     */
    public function getStats()
    {
        return new Stats($this->get('stats', true, array()), $this->api);
    }

    /**
     * @return array|float|int|mixed|null
     */
    public function isPreEULA()
    {
        return $this->get('eulaCoins', true, false);
    }

    /**
     * @return array|float|int|mixed|null
     */
    public function getLevel()
    {
        return $this->get('networkLevel', true, 0) + 1;
    }

    /**
     * @return array|float|int|mixed|null
     */
    public function getPrefix()
    {
        return $this->get('prefix', false, null);
    }

    /**
     * @return bool
     */
    public function isStaff()
    {
        $rank = $this->get('rank', true, 'NORMAL');
        if ($rank == 'NORMAL')
            return false;
        return true;
    }

    /**
     * get Current Multiplier, accounts for level and Pre-EULA rank
     * @return float|int
     */
    public function getMultiplier()
    {
        if ($this->getRank(false) == 'YOUTUBER') return 7;
        $ranks = array('DEFAULT', 'VIP', 'VIP+', 'MVP', 'MVP+');
        $pre = $this->getRank(true, true);
        if ($pre == 'NONE') $pre = 'DEFAULT';
        $flip = array_flip($ranks);
        $rankKey = $flip[$pre] + 1;
        $levelKey = floor($this->getLevel() / 25) + 1;
        return ($rankKey > $levelKey) ? $rankKey : $levelKey;
    }

    /**
     * get Rank
     * @param bool $package
     * @param bool $preEULA
     * @return mixed
     */
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
            if (!$this->isStaff()) return $this->getRank(true, $preEULA);
            $return = $this->get('rank', true);
        }
        if ($return == 'NONE' && $preEULA) {
            return $this->getRank($package, !$preEULA);
        }
        return str_replace('_', ' ', str_replace('_PLUS', '+', $return));
    }

    /**
     * get Player achievement points
     * @return int
     */
    public function getAchievementPoints()
    {
        return 0;
    }

    /**
     * @return array
     */
    public function getRanks()
    {
        $ranks = array(
            'ADMIN' => array(
                'prefix' => '§c[ADMIN]',
                'color' => '§c'
            ),
            'MODERATOR' => array(
                'prefix' => '§2[MOD]',
                'color' => '§2'
            ),
            'HELPER' => array(
                'prefix' => '§9[HELPER]',
                'color' => '§9'
            ),
            'JR HELPER' => array(
                'prefix' => '§9[JR HELPER]',
                'color' => '§9'
            ),
            'YOUTUBER' => array(
                'prefix' => '§6[YT]',
                'color' => '§6'
            ),
            'MVP+' => array(
                'prefix' => '§b[MVP§c+§b]',
                'color' => '§b'
            ),
            'MVP' => array(
                'prefix' => '§b[MVP]',
                'color' => '§b'
            ),
            'VIP+' => array(
                'prefix' => '§a[VIP§6+§a]',
                'color' => '§a'
            ),
            'VIP' => array(
                'prefix' => '§a[VIP]',
                'color' => '§a'
            ),
            'DEFAULT' => array(
                'prefix' => '§7',
                'color' => '§7'
            ),
            'NONE' => array(
                'prefix' => '§7[NONE]',
                'color' => '§7'
            )
        );

        return $ranks;
    }

    /**
     * @param string $rank
     *
     * @return mixed
     */
    public function getRankInfo($rank = 'NONE')
    {
        $rankInfo = $this->getRanks();
        if (!array_key_exists($rank, $rankInfo)) {
            $rank = 'NONE';
        }
        return $rankInfo[$rank];
    }

    /**
     * @param      $key
     * @param bool $implicit
     * @param null $default
     *
     * @return array|float|int|mixed|null
     */
    public function get($key, $implicit = false, $default = null)
    {
        if ($key == 'achievementPoints') {
            return $this->getAchievementPoints();
        } elseif ($key == 'multiplier') {
            return $this->getMultiplier();
        } elseif ($key == 'rank_packageRank') {
            return $this->getRank(true, true);
        } elseif ($key == 'rank_rank') {
            return $this->getRank(false, false);
        }

        if ($key == 'stats.MCGO.totalKills') {
            return $this->get('stats.MCGO.cop_kills', false, 0) + $this->get('stats.MCGO.criminal_kills', false, 0);
        }

        return parent::get($key, $implicit, $default);
    }
}

/**
 * Class Stats
 *
 * @package HypixelPHP
 */
class Stats extends HypixelObject
{
    /**
     * @param            $json
     * @param HypixelPHP $api
     */
    public function __construct($json, HypixelPHP $api)
    {
        parent::__construct(array('record' => $json), $api);
    }

    /**
     * @param $game
     *
     * @return GameStats
     */
    public function getGame($game)
    {
        $game = $this->get($game, true, null);
        return new GameStats($game, $this->api);
    }
}

/**
 * Class GameStats
 *
 * @package HypixelPHP
 */
class GameStats extends HypixelObject
{
    /**
     * @param            $json
     * @param HypixelPHP $api
     */
    public function __construct($json, HypixelPHP $api)
    {
        parent::__construct(array('record' => $json), $api);
    }

    /**
     * @return array|null
     */
    public function getPackages()
    {
        return $this->get('packages', false, array());
    }

    /**
     * @return array|null
     */
    public function getCoins()
    {
        return $this->get('coins', false, 0);
    }
}

/**
 * Class Session
 *
 * @package HypixelPHP
 */
class Session extends HypixelObject
{
    /**
     * @return array|null
     */
    public function getPlayers()
    {
        return $this->get('players', true);
    }

    /**
     * @return array|null
     */
    public function getGameType()
    {
        return $this->get('gameType', true);
    }

    /**
     * @return array|null
     */
    public function getServer()
    {
        return $this->get('server', true);
    }
}

/**
 * Class Friends
 *
 * @package HypixelPHP
 */
class Friends extends HypixelObject
{
    /**
     * @param            $json
     * @param HypixelPHP $api
     */
    public function __construct($json, HypixelPHP $api)
    {
        parent::__construct(array('record' => $json), $api);
    }
}

/**
 * Class Guild
 *
 * @package HypixelPHP
 */
class Guild extends HypixelObject
{
    private $members;

    /**
     * @return array|null
     */
    public function getName()
    {
        return $this->get('name', true);
    }

    /**
     * @return array|null
     */
    public function canTag()
    {
        return $this->get('canTag', true);
    }

    /**
     * @return array|null
     */
    public function getTag()
    {
        return $this->get('tag', true);
    }

    /**
     * @return array|null
     */
    public function getCoins()
    {
        return $this->get('coins', true);
    }

    /**
     * @return MemberList
     */
    public function getMemberList()
    {
        if ($this->members == null)
            $this->members = new MemberList($this->get('members'), $this->api);
        return $this->members;
    }

    /**
     * @return int
     */
    public function getMaxMembers()
    {
        $total = 25;
        $level = $this->get('memberSizeLevel', true, -1);
        if ($level >= 0) {
            $total += 5 * $level;
        }
        return $total;
    }

    /**
     * @return int
     */
    public function getMemberCount()
    {
        return $this->getMemberList()->getMemberCount();
    }

    /**
     * get coin history of Guild or Player in Guild
     * @param null $player
     * @return array
     */
    public function getGuildCoinHistory($player = null)
    {
        $coinHistory = array();
        $record = $this->getRecord();
        if ($player != null) {
            /* @var $player Player */
            $memberList = $this->getMemberList()->getList();
            foreach ($memberList as $rank => $list) {
                foreach ($list as $p) {
                    if (isset($p['uuid'])) {
                        if ($player->getUUID() == $p['uuid']) {
                            $record = $p;
                        }
                    }
                }
            }
        }
        foreach ($record as $key => $val) {
            if (strpos($key, 'dailyCoins') !== false) {
                $coinHistory[substr($key, strpos($key, '-') + 1)] = $val;
            }
        }
        return $coinHistory;
    }
}

/**
 * Class MemberList
 *
 * @package HypixelPHP
 */
class MemberList extends HypixelObject
{
    private $list;
    private $count;

    /**
     * @param            $json
     * @param HypixelPHP $api
     */
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

    /**
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @return int
     */
    public function getMemberCount()
    {
        return $this->count;
    }
}

/**
 * Class GameTypes
 *
 * @package HypixelPHP
 */
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

    /**
     * @param $id
     *
     * @return GameType|null
     */
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

    /**
     * @return array
     */
    public static function getAllTypes()
    {
        $obj = new \ReflectionClass ('\HypixelPHP\GameTypes');
        return $obj->getConstants();
    }
}

/**
 * Class GameType
 *
 * @package HypixelPHP
 */
class GameType
{
    private $db, $name, $short, $id;

    /**
     * @param $db
     * @param $name
     * @param $short
     * @param $id
     */
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

/**
 * Class Boosters
 *
 * @package HypixelPHP
 */
class Boosters extends HypixelObject
{
    /**
     * @param int $gameType
     * @param int $max
     *
     * @return array
     */
    public function getQueue($gameType = GameTypes::ARCADE, $max = 999)
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

    /**
     * @param Player $player
     * @return Booster[]
     */
    public function getBoosters(Player $player)
    {
        $boosters = array();
        foreach ($this->JSONArray['record'] as $boosterInfo) {
            if (isset($boosterInfo['purchaserUuid'])) {
                if ($boosterInfo['purchaserUuid'] == $player->getUUID()) {
                    $booster = new Booster($boosterInfo, $this->api);
                    array_push($boosters, $booster);
                }
            }
        }
        return $boosters;
    }
}

/**
 * Class Booster
 *
 * @package HypixelPHP
 */
class Booster
{
    private $info;
    private $api;

    /**
     * @param            $info
     * @param HypixelPHP $api
     */
    public function __construct($info, HypixelPHP $api)
    {
        $this->info = $info;
        $this->api = $api;
    }

    /**
     * @return Player
     */
    public function getOwner()
    {
        $oldTime = $this->api->getCacheTime();
        $this->api->setCacheTime(HypixelPHP::MAX_CACHE_TIME - 1);
        $player = $this->api->getPlayer(array(
            'name' => (isset($this->info['purchaser']) ? $this->info['purchaser'] : null),
            'uuid' => (isset($this->info['purchaserUuid']) ? $this->info['purchaserUuid'] : null)
        ));
        $this->api->setCacheTime($oldTime);
        if ($player != null) {
            return $player;
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

/**
 * Class Leaderboards
 *
 * @package HypixelPHP
 */
class Leaderboards extends HypixelObject
{
    /**
     * @param            $json
     * @param HypixelPHP $api
     */
    public function __construct($json, HypixelPHP $api)
    {
        parent::__construct(array('record' => $json), $api);
    }
}