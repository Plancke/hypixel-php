<?php
namespace HypixelPHP;

use DateTime;

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
    private $getUrlError = null;
    const MAX_CACHE_TIME = 999999999999;

    /**
     * @param array $input
     */
    public function  __construct($input = [])
    {
        $this->options = array_merge(
            [
                'api_key' => '',
                'cache_times' => [
                    'overall' => 900, // 15 min
                    'uuid' => 864000, // 1 day
                ],
                'timeout' => 2,
                'cache_folder_player' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/player',
                'cache_folder_guild' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/guild',
                'cache_folder_friends' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/friends',
                'cache_folder_sessions' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/sessions',
                'cache_folder_keyInfo' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/keyInfo/',
                'cache_boosters' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/boosters.json',
                'cache_leaderboards' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/leaderboards.json',
                'log_folder' => $_SERVER['DOCUMENT_ROOT'] . '/logs/HypixelAPI',
                'logging' => true,
                'debug' => true,
                'use_curl' => true
            ],
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
        if (!file_exists($this->options['log_folder'])) {
            mkdir($this->options['log_folder'], 0777, true);
        }

        $old_debug = $this->options['debug'];
        $this->set(['debug' => false]);
        $this->setCacheTime($this->getCacheTime(), 'original');
        $this->set(['debug' => $old_debug]);
    }

    /**
     * @param $input
     */
    public function set($input)
    {
        foreach ($input as $key => $val) {
            if ($key != 'api_key' && $key != 'debug') {
                if ($this->options[$key] != $val) {
                    if (is_array($val)) {
                        $this->debug('Setting ' . $key . ' to ' . json_encode($val));
                    } else {
                        $this->debug('Setting ' . $key . ' to ' . $val);
                    }
                }
            }
            $this->options[$key] = $val;
        }
    }

    /**
     * @param $message
     * @param bool $log
     */
    public function debug($message, $log = true)
    {
        if ($this->options['debug']) {
            echo '<!-- ' . $message . ' -->';
        }
        if ($log && $this->options['logging']) {
            $this->log($message);
        }
    }

    /**
     * @param $key
     */
    public function setKey($key)
    {
        $this->set(['api_key' => $key]);
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
    public function hasPaid($name, $url = 'https://mcapi.ca/other/haspaid/%s')
    {
        $hasPaid = $this->getUrlContents(sprintf($url, $name));
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
        $errorOut = ["success" => false, 'cause' => 'Timeout'];
        if ($this->options['use_curl']) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout * 1000);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeout * 1000);
            $curlOut = curl_exec($ch);
            if ($curlOut === false) {
                $errorOut['cause'] = curl_error($ch);
                $this->getUrlError = ['errorCause' => $errorOut['cause']];
                curl_close($ch);
                return $errorOut;
            }
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $this->getUrlError = ['errorCause' => null, 'status' => $status];
            if ($status != '200') {
                return $errorOut;
            }
            $json_out = json_decode($curlOut, true);
            $this->getUrlError = ['status' => $status, 'throttle' => isset($json_out['throttle']) ? $json_out['throttle'] : false];
            return $json_out;
        } else {
            $ctx = stream_context_create([
                    'https' => [
                        'timeout' => $timeout
                    ]
                ]
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
        if ($for == null) {
            $for = 'overall';
        }
        if (isset($this->options['cache_times'][$for])) {
            return $this->options['cache_times'][$for];
        }
        return HypixelPHP::MAX_CACHE_TIME;
    }

    /**
     * Returns the currently set cache threshold
     * @return int
     */
    public function getOriginalCacheTime()
    {
        return $this->getCacheTime('original');
    }

    /**
     * @param int $cache_time
     * @param null $for
     */
    public function setCacheTime($cache_time = 600, $for = null)
    {
        $cache_times = $this->options['cache_times'];
        if ($for == null) {
            $for = 'overall';
        }
        $cache_times[$for] = $cache_time;
        $this->set(['cache_times' => $cache_times]);
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
        $scanDir = array_diff(scandir($dirName), ['.', '..']);
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
            $return = ["success" => false, 'cause' => 'Max Cache Time!'];
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
    public function getPlayer($keyPair = [])
    {
        $pairs = array_merge(
            [
                'name' => null,
                'uuid' => null,
                'unknown' => null
            ],
            $keyPair
        );

        foreach ($pairs as $key => $val) {
            if ($key == 'uuid') $val = str_replace("-", "", $val);
            if ($val != null && $val != '') {
                $filename = $this->options['cache_folder_player'] . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $this->getCacheFileName($val) . '.json';
                if ($key == 'uuid') {
                    if (!strlen($val) == 32) continue;
                    $content = $this->getCache($filename);
                    if ($content != null) {
                        $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                        if (time() - $this->getCacheTime() < $timestamp) {
                            return new Player($content, $this);
                        }
                    }

                    $response = $this->fetch('player', $key, $val);
                    if ($response['success'] == true) {
                        $PLAYER = new Player([
                            'record' => $response['player'],
                            'extra' => $content['extra']
                        ], $this);
                        $PLAYER->setExtra(['filename' => $filename]);
                        $this->setCache($filename, $PLAYER);
                        return $PLAYER;
                    }
                } else if ($key == 'name') {
                    if (file_exists($filename) || $this->hasPaid($val)) {
                        $uuid = $this->getUUID($val);
                        return $this->getPlayer(['uuid' => $uuid]);
                    }
                } else if ($key == 'unknown') {
                    $this->debug('Determining type.', false);
                    $type = InputType::getType($val);
                    if ($type == InputType::USERNAME) {
                        $this->debug('Input is username, fetching UUID.', false);
                        $uuid = $this->getUUID($val);
                    } else if ($type == InputType::UUID) {
                        $uuid = $val;
                    } else {
                        return null;
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
    public function getGuild($keyPair = [])
    {
        $pairs = array_merge(
            [
                'player' => null,
                'byUuid' => null,
                'byPlayer' => null,
                'byName' => null,
                'id' => null
            ],
            $keyPair
        );

        foreach ($pairs as $key => $val) {
            if ($val != null && $val != '') {
                if ($key == 'player' && $val instanceof Player) {
                    /* @var $val Player */
                    return $this->getGuild(['byUuid' => $val->getUUID()]);
                }

                $filename = $this->options['cache_folder_guild'] . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $this->getCacheFileName($val) . '.json';

                if ($key == 'byPlayer' || $key == 'byName' || $key == 'byUuid') {
                    if ($key == 'byPlayer') {
                        $uuid = $this->getUuid($val);
                        return $this->getGuild(['byUuid' => $uuid]);
                    }

                    $content = $this->getCache($filename);
                    if ($content != null) {
                        $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                        if (time() - $this->getCacheTime() < $timestamp) {
                            if (isset($content['guild'])) {
                                return $this->getGuild(['id' => $content['guild']]);
                            }
                            continue;
                        }
                    }

                    $response = $this->fetch('findGuild', $key, $val, 5);
                    if ($response['success'] == true) {
                        $content = ['timestamp' => time(), 'guild' => $response['guild']];
                        $this->setFileContent($filename, json_encode($content));
                        return $this->getGuild(['id' => $response['guild']]);
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
                    if ($response['success'] == true) {
                        $GUILD = new Guild([
                            'record' => $response['guild'],
                            'extra' => $content['extra']
                        ], $this);
                        $GUILD->setExtra(['filename' => $filename]);
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
    public function getSession($keyPair = [])
    {
        $pairs = array_merge(
            [
                'player' => null,
                'name' => null,
                'uuid' => null
            ],
            $keyPair
        );

        foreach ($pairs as $key => $val) {
            if ($val != null && $val != '') {
                if ($key == 'player' && $val instanceof Player) {
                    /* @var $val Player */
                    return $this->getSession(['uuid' => $val->getUUID()]);
                }

                $filename = $this->options['cache_folder_sessions'] . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $this->getCacheFileName($val) . '.json';

                if ($key == 'name') {
                    if (file_exists($filename) || $this->hasPaid($val)) {
                        $uuid = $this->getUUID($val);
                        return $this->getSession(['uuid' => $uuid]);
                    }
                } elseif ($key == 'uuid') {
                    $content = $this->getCache($filename);
                    if ($content != null) {
                        $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                        if (time() - $this->getCacheTime() < $timestamp) {
                            return new Session($content, $this);
                        }
                    }

                    $response = $this->fetch('session', $key, $val);
                    if ($response['success'] == true) {
                        $SESSION = new Session([
                            'record' => $response['session'],
                            'extra' => $content['extra']
                        ], $this);
                        $SESSION->setExtra(['filename' => $filename]);
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
    public function getFriends($keyPair = [])
    {
        $pairs = array_merge(
            [
                'player' => null,
                'name' => null,
                'uuid' => null
            ],
            $keyPair
        );

        foreach ($pairs as $key => $val) {
            if ($val != null && $val != '') {
                if ($key == 'player' && $val instanceof Player) {
                    /* @var $val Player */
                    return $this->getFriends(['uuid' => $val->getUUID()]);
                }

                $filename = $this->options['cache_folder_friends'] . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $this->getCacheFileName($val) . '.json';

                if ($key == 'name') {
                    if (file_exists($filename) || $this->hasPaid($val)) {
                        $uuid = $this->getUUID($val);
                        return $this->getFriends(['uuid' => $uuid]);
                    }
                } elseif ($key == 'uuid') {
                    $content = $this->getCache($filename);
                    if ($content != null) {
                        $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                        if (time() - $this->getCacheTime() < $timestamp) {
                            return new Friends($content, $this);
                        }
                    }

                    $response = $this->fetch('friends', $key, $val);
                    if ($response['success'] == true) {
                        $FRIENDS = new Friends([
                            'record' => $response['records'],
                            'extra' => $content['extra']
                        ], $this);
                        $FRIENDS->setExtra(['filename' => $filename]);
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
        if ($response['success'] == true) {
            $BOOSTERS = new Boosters([
                'record' => $response['boosters'],
                'extra' => $content['extra']
            ], $this);
            $BOOSTERS->setExtra(['filename' => $filename]);
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
            $LEADERBOARDS = new Leaderboards([
                'record' => $response['leaderboards'],
                'extra' => $content['extra']
            ], $this);
            $LEADERBOARDS->setExtra(['filename' => $filename]);
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
        $filename = $this->options['cache_folder_keyInfo'] . $this->getCacheFileName($this->getKey());
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
            $content = ['timestamp' => time(), 'record' => $response['record']];
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
            $content['extra'] = [];
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
        if ($string == null) return null;
        $MCColors = [
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
            "a" => "#3CE63C",
            "b" => "#55FFFF",
            "c" => "#FF5555",
            "d" => "#FF55FF",
            "e" => "#FFFF55",
            "f" => "#FFFFFF"
        ];

        if (strpos($string, "§") === false) {
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
     * Function to get and cache UUID from username.
     * @param string $username
     * @param string $url
     *
     * @return string|bool
     */
    public function getUUID($username, $url = 'https://api.mojang.com/users/profiles/minecraft/%s')
    {
        $uuidURL = sprintf($url, $username);
        $filename = $this->options['cache_folder_player'] . DIRECTORY_SEPARATOR . 'name' . DIRECTORY_SEPARATOR . $this->getCacheFileName($username) . '.json';

        $content = $this->getCache($filename);
        if ($content != null) {
            $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
            if (time() - $this->getCacheTime('uuid') < $timestamp) {
                if (isset($content['uuid'])) return $content['uuid'];
            }
        }

        $response = $this->getUrlContents($uuidURL);
        if (isset($response['id'])) {
            $this->debug('UUID for username fetched!');
            $content = [
                'timestamp' => time(),
                'name' => $response['name'],
                'uuid' => $response['id']
            ];
            $this->setFileContent($filename, json_encode($content));
            $this->debug($username . ' => ' . $response['id']);
            return $response['id'];
        }

        if ($this->getCacheTime('uuid') < self::MAX_CACHE_TIME) {
            $this->setCacheTime(self::MAX_CACHE_TIME, 'uuid');
            return $this->getUUID($username, $url);
        }
        return false;
    }

    /**
     * Get the last error and status code associated with the last cURL fetch.
     * @return null|array
     */
    public function getUrlError()
    {
        return $this->getUrlError;
    }

}

class InputType
{
    const UUID = 0;
    const USERNAME = 1;

    /**
     * Determine if the $input is a playername or UUID.
     * UUIDs have a length of 32 chars, and usernames a max. length of 16 chars.
     * @param string $input
     *
     * @return int
     */
    public static function getType($input)
    {
        if (strlen($input) === 32) return InputType::UUID;
        return InputType::USERNAME;
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
            $this->JSONArray = [];
        }
        if (!array_key_exists('extra', $this->JSONArray) || $this->JSONArray['extra'] == null) {
            $this->JSONArray['extra'] = [];
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
     * @param $key
     * @return Integer
     */
    public function getInt($key)
    {
        return $this->get($key, true, 0);
    }

    /**
     * @param $key
     * @return array
     */
    public function getArray($key)
    {
        return $this->get($key, true, []);
    }

    /**
     * @return array|null
     */
    public function getID()
    {
        return $this->get('_id');
    }

    /**
     * @return bool
     */
    public function isCached()
    {
        return abs(time() - $this->getCachedTime()) > 1;
    }

    /**
     * @param int $extra
     * @return bool
     */
    public function isCacheExpired($extra = 0)
    {
        return time() - $this->api->getOriginalCacheTime() - $extra > $this->getCachedTime();
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
            $this->api->debug('Saving cache file', false);
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
    private $guild;

    /**
     * get Session of Player
     * @return Session|null
     */
    public function getSession()
    {
        return $this->api->getSession(['player' => $this]);
    }

    /**
     * get Friends of Player
     * @return Friends|null
     */
    public function getFriends()
    {
        return $this->api->getFriends(['player' => $this]);
    }

    /**
     * get Guild of Player
     * @return Guild|null
     */
    public function getGuild()
    {
        if ($this->guild == null) {
            $this->guild = $this->api->getGuild(['player' => $this]);
        }
        return $this->guild;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if ($this->get('displayname', true) != null) {
            return $this->get('displayname', true);
        } else {
            $aliases = $this->get('knownAliases', true, []);
            if (sizeof($aliases) == 0) {
                return $this->get('playername', true);
            }
            return end($aliases);
        }
    }

    /**
     * get Colored name of Player, with prefix or not
     * @param bool $prefix
     * @param bool $guildTag
     * @return string
     */
    public function getFormattedName($prefix = true, $guildTag = false)
    {
        $rank = $this->getRank(false);
        $out = $rank->getColor() . $this->getName();
        if ($prefix) {
            $out = ($this->getPrefix() != null ? $this->getPrefix() : $rank->getPrefix()) . ' ' . $this->getName();
        }
        if ($guildTag) {
            $out .= $this->getGuildTag() != null ? ' §7[' . $this->getGuildTag() . ']' : '';
        }
        return $this->api->parseColors($out);
    }

    /**
     * @return string
     */
    public function getGuildTag()
    {
        $guild = $this->getGuild();
        if ($guild != null) {
            if ($guild->canTag()) {
                return $guild->getTag();
            }
        }
        return null;
    }

    /**
     * @return string
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
        return new Stats($this->getArray('stats'), $this->api);
    }

    /**
     * @return bool
     */
    public function isPreEULA()
    {
        return $this->get('eulaCoins', true, false);
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->getInt('networkLevel') + 1;
    }

    /**
     * @return string|null
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
     * @return int
     */
    public function getMultiplier()
    {
        if ($this->getRank(false)->getId() == RankTypes::YOUTUBER) return 7;
        $pre = $this->getRank(true, true, false);
        if ($pre != null) {
            $eulaMultiplier = 1;
            if (array_key_exists('eulaMultiplier', $pre->getOptions())) {
                $eulaMultiplier = $pre->getOptions()['eulaMultiplier'];
            }
            $levelMultiplier = min(floor($this->getLevel() / 25) + 1, 5);
            return ($eulaMultiplier > $levelMultiplier) ? $eulaMultiplier : $levelMultiplier;
        }
        return 1;
    }

    /**
     * get Rank
     * @param bool $package
     * @param bool $preEULA
     * @param bool $doSwap
     * @return Rank
     */
    public function getRank($package = true, $preEULA = false, $doSwap = true)
    {
        $return = 'DEFAULT';
        if ($package) {
            $keys = ['newPackageRank', 'packageRank'];
            if ($preEULA) $keys = array_reverse($keys);
            if (!$this->isStaff()) {
                if (!$this->isPreEULA()) {
                    if ($this->get($keys[0], true) != null) {
                        $return = $this->get($keys[0], true);
                    }
                } else {
                    foreach ($keys as $key) {
                        if ($this->get($key, true) != null) {
                            $return = $this->get($key, true);
                            break;
                        }
                    }
                }
            } else {
                foreach ($keys as $key) {
                    if ($this->get($key, true) != null) {
                        $return = $this->get($key, true);
                        break;
                    }
                }
            }
        } else {
            if (!$this->isStaff()) return $this->getRank(true, $preEULA);
            $return = $this->get('rank', true);
        }
        if ($return == 'NONE' && $preEULA && $doSwap) {
            return $this->getRank($package, !$preEULA);
        }
        $returnRank = RankTypes::fromName($return);
        if ($returnRank == null)
            $returnRank = RankTypes::fromID(RankTypes::NON_DONOR);
        return $returnRank;
    }

    function getUnderlyingRank($preEula = false)
    {
        if ($this->isStaff()) {
            return $this->getRank(true, $preEula);
        }
        return null;
    }

    /**
     * @return array
     */
    public function getAchievementPoints()
    {
        return 0;
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
        return parent::get($key, $implicit, $default);
    }
}

class RankTypes
{
    const NORMAL = 0;
    const NON_DONOR = 1;
    const VIP = 2;
    const VIP_PLUS = 3;
    const MVP = 4;
    const MVP_PLUS = 5;

    const ADMIN = 100;
    const MODERATOR = 90;
    const HELPER = 80;
    const JR_HELPER = 70;
    const YOUTUBER = 60;

    /**
     * @param $id
     *
     * @return Rank|null
     */
    public static function fromID($id)
    {
        switch ($id) {
            case RankTypes::NORMAL:
                return new Rank(RankTypes::NORMAL, 'NONE', [
                    'prefix' => '§7',
                    'color' => '§7',
                ]);
            case RankTypes::NON_DONOR:
                return new Rank(RankTypes::NON_DONOR, 'NON_DONOR', [
                    'prefix' => '§7',
                    'color' => '§7'
                ]);
            case RankTypes::VIP:
                return new Rank(RankTypes::VIP, 'VIP', [
                    'prefix' => '§a[VIP]',
                    'color' => '§a',
                    'eulaMultiplier' => 2
                ]);
            case RankTypes::VIP_PLUS:
                return new Rank(RankTypes::VIP_PLUS, 'VIP_PLUS', [
                    'prefix' => '§a[VIP§6+§a]',
                    'color' => '§a',
                    'eulaMultiplier' => 3
                ]);
            case RankTypes::MVP:
                return new Rank(RankTypes::MVP, 'MVP', [
                    'prefix' => '§b[MVP]',
                    'color' => '§b',
                    'eulaMultiplier' => 4
                ]);
            case RankTypes::MVP_PLUS:
                return new Rank(RankTypes::MVP_PLUS, 'MVP_PLUS', [
                    'prefix' => '§b[MVP§c+§b]',
                    'color' => '§b',
                    'eulaMultiplier' => 5
                ]);
            case RankTypes::YOUTUBER:
                return new Rank(RankTypes::YOUTUBER, 'YOUTUBER', [
                    'prefix' => '§6[YT]',
                    'color' => '§6',
                    'eulaMultiplier' => 7
                ]);
            case RankTypes::JR_HELPER:
                return new Rank(RankTypes::JR_HELPER, 'JR_HELPER', [
                    'prefix' => '§9[JR HELPER]',
                    'color' => '§9'
                ], true);
            case RankTypes::HELPER:
                return new Rank(RankTypes::HELPER, 'HELPER', [
                    'prefix' => '§9[HELPER]',
                    'color' => '§9'
                ], true);
            case RankTypes::MODERATOR:
                return new Rank(RankTypes::MODERATOR, 'MODERATOR', [
                    'prefix' => '§2[MOD]',
                    'color' => '§2'
                ], true);
            case RankTypes::ADMIN:
                return new Rank(RankTypes::ADMIN, 'ADMIN', [
                    'prefix' => '§c[ADMIN]',
                    'color' => '§c'
                ], true);
            default:
                return null;
        }
    }

    public static function fromName($db)
    {
        foreach (RankTypes::getAllTypes() as $id) {
            $rank = RankTypes::fromID($id);
            if ($rank != null) {
                if ($rank->getName() == $db) {
                    return $rank;
                }
            }
        }
        return null;
    }

    /**
     * @return array
     */
    public static function getAllTypes()
    {
        $obj = new \ReflectionClass ('\HypixelPHP\RankTypes');
        return $obj->getConstants();
    }
}

class Rank
{
    private $name, $id, $options, $staff;

    /**
     * @param $id
     * @param $name
     * @param $options
     * @param bool $staff
     */
    public function __construct($id, $name, $options, $staff = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->options = $options;
        $this->staff = $staff;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCleanName()
    {
        if ($this->name == 'NON_DONOR' || $this->name == 'NONE') return 'DEFAULT';
        return str_replace("_", ' ', str_replace('_PLUS', '+', $this->name));
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function isStaff()
    {
        return $this->staff;
    }

    public function getPrefix()
    {
        return isset($this->options['prefix']) ? $this->options['prefix'] : null;
    }

    public function getColor()
    {
        return isset($this->options['color']) ? $this->options['color'] : null;
    }

    public function __toString()
    {
        return json_encode([$this->name => $this->options]);
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
        parent::__construct(['record' => $json], $api);
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

    public function getGameFromID($id)
    {
        $gameType = GameTypes::fromID($id);
        if ($gameType != null) {
            return $this->getGame($gameType->getDb());
        }
        return null;
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
        parent::__construct(['record' => $json], $api);
    }

    /**
     * @return array|null
     */
    public function getPackages()
    {
        return $this->getArray('packages');
    }

    /**
     * @param $package
     * @return bool
     */
    public function hasPackage($package)
    {
        return in_array($package, $this->getArray('packages'));
    }

    /**
     * @return int
     */
    public function getCoins()
    {
        return $this->getInt('coins');
    }

    /**
     * @param $stat
     * @return array|null|string|int
     */
    public function getWeeklyStat($stat)
    {
        return $this->get($stat . '_' . TimeUtils::getWeeklyOscillation());
    }

    /**
     * @param $stat
     * @return array|null|string|int
     */
    public function getMonthlyStat($stat)
    {
        return $this->get($stat . '_' . TimeUtils::getMonthlyOscillation());
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
     * @return array
     */
    public function getPlayers()
    {
        return $this->getArray('players');
    }

    /**
     * @return array|null
     */
    public function getGameType()
    {
        return $this->get('gameType', true);
    }

    /**
     * @return string
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

}

/**
 * Class Guild
 *
 * @package HypixelPHP
 */
class Guild extends HypixelObject
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->get('name', true, '');
    }

    /**
     * @return bool
     */
    public function canTag()
    {
        return $this->get('canTag', true, false);
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->api->parseColors($this->get('tag', true, ''));
    }

    /**
     * @return int
     */
    public function getCoins()
    {
        return $this->getInt('coins');
    }

    /**
     * @return MemberList
     */
    public function getMemberList()
    {
        return new MemberList($this->get('members'), $this->api);
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
     * @return array
     */
    public function getGuildCoinHistory()
    {
        $coinHistory = [];
        $record = $this->getRecord();
        foreach ($record as $key => $val) {
            if (strpos($key, 'dailyCoins') !== false) {
                $EXPLOSION = explode('-', $key);
                $coinHistory[$EXPLOSION[1] . '-' . ($EXPLOSION[2] + 1) . '-' . $EXPLOSION[3]] = $val;
            }
        }

        $sortHistory = [];
        foreach ($coinHistory as $DATE => $AMOUNT) {
            array_push($sortHistory, [$DATE, $AMOUNT]);
        }

        usort($sortHistory, function ($a, $b) {
            $ad = new DateTime($a[0]);
            $bd = new DateTime($b[0]);

            if ($ad == $bd) {
                return 0;
            }

            return $ad < $bd ? 0 : 1;
        });

        return $sortHistory;
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
        parent::__construct(['record' => $json], $api);

        $list = ["GUILDMASTER" => [], "OFFICER" => [], "MEMBER" => []];
        $this->count = sizeof($json);
        foreach ($json as $player) {
            $rank = $player['rank'];
            if (!in_array($rank, array_keys($list))) {
                $list[$rank] = [];
            }

            $coinHistory = [];
            foreach ($player as $key => $val) {
                if (strpos($key, 'dailyCoins') !== false) {
                    $EXPLOSION = explode('-', $key);
                    $coinHistory[$EXPLOSION[1] . '-' . ($EXPLOSION[2] + 1) . '-' . $EXPLOSION[3]] = $val;
                    unset($player[$key]);
                }
            }
            $player['coinHistory'] = $coinHistory;

            array_push($list[$rank], new GuildMember($player, $api));
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
 * Class GuildMember
 *
 * @package HypixelPHP
 */
class GuildMember
{
    private $coinHistory;
    private $uuid, $name;
    private $joined;
    private $api;

    /**
     * @param $member
     * @param HypixelPHP $api
     */
    public function __construct($member, $api)
    {
        if (isset($member['coinHistory']))
            $this->coinHistory = $member['coinHistory'];
        if (isset($member['uuid']))
            $this->uuid = $member['uuid'];
        if (isset($member['name']))
            $this->name = $member['name'];
        if (isset($member['joined']))
            $this->joined = $member['joined'];
        $this->api = $api;
    }

    /**
     * @return Player
     * @internal param $HypixelPHP
     */
    public function getPlayer()
    {
        if (isset($this->uuid)) {
            return $this->api->getPlayer(['uuid' => $this->uuid]);
        } else if (isset($this->name)) {
            return $this->api->getPlayer(['name' => $this->name]);
        }
        return null;
    }

    /**
     * @return array
     */
    public function getCoinHistory()
    {
        return $this->coinHistory;
    }

    /**
     * @return int
     */
    public function getJoinTimeStamp() {
        return $this->joined;
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
    const HUNGERGAMES = 5;
    const TNTGAMES = 6;
    const VAMPIREZ = 7;
    const WALLS3 = 13;
    const ARCADE = 14;
    const ARENA = 17;
    const UHC = 20;
    const MCGO = 21;
    const BATTLEGROUND = 23;
    const GINGERBREAD = 25;
    const SKYWARS = 51;

    /**
     * @param $id
     *
     * @return GameType|null
     */
    public static function fromID($id)
    {
        switch ($id) {
            case 2:
                return new GameType('Quake', 'Quake', 'Quake', 2);
            case 3:
                return new GameType('Walls', 'Walls', 'Walls', 3);
            case 4:
                return new GameType('Paintball', 'Paintball', 'PB', 4);
            case 5:
                return new GameType('HungerGames', 'Blitz Survival Games', 'BSG', 5);
            case 6:
                return new GameType('TNTGames', 'TNT Games', 'TNT', 6);
            case 7:
                return new GameType('VampireZ', 'VampireZ', 'VampZ', 7);
            case 13:
                return new GameType('Walls3', 'MegaWalls', 'MW', 13);
            case 14:
                return new GameType('Arcade', 'Arcade', 'Arcade', 14);
            case 17:
                return new GameType('Arena', 'Arena', 'Arena', 17);
            case 20:
                return new GameType('UHC', 'UHC Champions', 'UHC', 20);
            case 21:
                return new GameType('MCGO', 'Cops and Crims', 'CaC', 21);
            case 23:
                return new GameType('Battleground', 'Warlords', 'Warlords', 23);
            case 25:
                return new GameType('GingerBread', 'Turbo Kart Racers', 'TKR', 25);
            case 51:
                return new GameType('SkyWars', 'SkyWars', 'SkyWars', 51);
            default:
                return null;
        }
    }

    public static function fromDbName($db)
    {
        foreach (GameTypes::getAllTypes() as $id) {
            $gameType = GameTypes::fromID($id);
            if ($gameType != null) {
                if ($gameType->getDb() == $db) {
                    return $gameType;
                }
            }
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
        $return = [
            'boosters' => [],
            'total' => 0
        ];
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
        $boosters = [];
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
        $player = $this->api->getPlayer([
            'name' => (isset($this->info['purchaser']) ? $this->info['purchaser'] : null),
            'uuid' => (isset($this->info['purchaserUuid']) ? $this->info['purchaserUuid'] : null)
        ]);
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
}

/**
 * Class TimeUtils
 *
 * @package HypixelPHP
 */
class TimeUtils
{
    /**
     * @return string
     */
    public static function getWeeklyOscillation()
    {
        date_default_timezone_set("America/New_York");
        $epoch = 1417237200000;
        $milli = round(microtime(true) * 1000);

        $delta = abs($milli - $epoch);
        $osc = $delta / 604800000;

        return $osc % 2 == 0 ? "a" : "b";
    }

    /**
     * @return string
     */
    public static function getMonthlyOscillation()
    {
        date_default_timezone_set("America/New_York");
        $epoch = 1417410000000;

        $dateStart = new DateTime(date("Y-m-d"));
        $dateEnd = new DateTime(date("Y-m-d", $epoch / 1000));

        $diffYear = $dateEnd->format("Y") - $dateStart->format("Y");
        /* @var $diffYear int */
        $diffMonth = $diffYear * 12 + TimeUtils::getJavaMonth($dateEnd) - TimeUtils::getJavaMonth($dateStart);

        return $diffMonth % 2 == 0 ? "a" : "b";
    }

    /**
     * @param DateTime $date
     * @return int
     */
    public static function getJavaMonth(DateTime $date)
    {
        date_default_timezone_set("America/New_York");
        return $date->format("n") - 1;
    }
}
