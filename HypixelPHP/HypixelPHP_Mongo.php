<?php
namespace HypixelPHP_Mongo;

use DateTime;
use MongoClient;

/**
 * HypixelPHP
 *
 * @author Plancke
 * @version 2.0.1
 * @link  http://plancke.nl
 *
 */
class HypixelPHP {
    private $options;
    private $getUrlErrors = [];
    public $MONGO_CLIENT;
    const MAX_CACHE_TIME = 999999999999;

    /**
     * @param array $input
     */
    public function __construct($input = []) {
        $this->options = array_merge(
            [
                'api_key' => '',
                'cache_times' => [
                    CACHE_TIMES::OVERALL => 600,
                    CACHE_TIMES::PLAYER => 600,
                    CACHE_TIMES::UUID => 864000,
                    CACHE_TIMES::UUID_NOT_FOUND => 600,
                    CACHE_TIMES::GUILD => 600,
                    CACHE_TIMES::GUILD_NOT_FOUND => 600,
                ],
                'timeout' => 1000,
                'log_folder' => $_SERVER['DOCUMENT_ROOT'] . '/logs/HypixelAPI',
                'cache_boosters' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/boosters.json',
                'cache_leaderboards' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/leaderboards.json',
                'achievements_file' => $_SERVER['DOCUMENT_ROOT'] . '/assets/achievements.json',
                'logging' => false,
                'debug' => false,
                'use_curl' => true
            ],
            $input
        );

        if (!file_exists($this->options['log_folder'])) {
            mkdir($this->options['log_folder'], 0777, true);
        }

        $this->options['cache_times_original'] = $this->options['cache_times'];
        $this->MONGO_CLIENT = new MongoClient();
    }

    /**
     * @param $input
     */
    public function set($input) {
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
    public function debug($message, $log = true) {
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
    public function setKey($key) {
        $this->set(['api_key' => $key]);
    }

    public function getKey() {
        return $this->options['api_key'];
    }

    /**
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Checks if $name is a paid account
     * @param $name
     * @param string $url
     * @return bool
     */
    public function hasPaid($name, $url = 'https://mcapi.ca/other/haspaid/%s') {
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
    public function getUrlContents($url, $timeout = -1) {
        if ($timeout == -1) {
            $timeout = $this->getOptions()['timeout'];
        }
        $errorOut = ['success' => false];
        if ($this->options['use_curl']) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeout);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            $curlOut = curl_exec($ch);
            $errorOut['cause'] = curl_error($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $errorOut['status'] = $status;
            curl_close($ch);
            if ($curlOut === false || $status != '200') {
                array_push($this->getUrlErrors, $errorOut);
                return $errorOut;
            }
            $json_out = json_decode($curlOut, true);
            if (isset($json_out['throttle'])) {
                $errorOut['throttle'] = $json_out['throttle'];
                array_push($this->getUrlErrors, $errorOut);
            }
            return $json_out;
        } else {
            $ctx = stream_context_create([
                'https' => ['timeout' => $timeout / 1000]
            ]);
            $out = file_get_contents($url, 0, $ctx);
            if ($out === false) {
                return $errorOut;
            }
            return json_decode($out, true);
        }
    }

    /**
     * Returns the currently set cache threshold
     * @param null|string $for
     * @param bool $original
     * @return int
     */
    public function getCacheTime($for = CACHE_TIMES::OVERALL, $original = false) {
        $key = 'cache_times' . ($original ? '_original' : '');
        if (isset($this->options[$key][$for])) {
            return $this->options[$key][$for];
        }
        return HypixelPHP::MAX_CACHE_TIME;
    }

    /**
     * @param int $cache_time
     * @param array $for
     */
    public function setCacheTime($cache_time = 600, $for = [CACHE_TIMES::OVERALL]) {
        $cache_times = $this->options['cache_times'];
        if (!is_array($for)) {
            $for = [$for]; // Backwards compatibility
        }
        foreach ($for as $f) {
            $cache_times[$f] = $cache_time;
        }
        $this->set(['cache_times' => $cache_times]);
    }

    /**
     * Set cache time for all of them
     * @param int $cache_time
     */
    public function setAllCacheTimes($cache_time = 600) {
        $this->setCacheTime($cache_time, CACHE_TIMES::getAllTypes());
    }

    /**
     * Log $string to log files
     * Directory setup:
     *  - LOG_FOLDER/DATE/1.log
     *  - LOG_FOLDER/DATE/2.log
     * separated every 25MB
     * @param $string
     */
    public function log($string) {
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
    public function fetch($request, $key = null, $val = null, $timeout = -1) {
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
            $this->setAllCacheTimes(HypixelPHP::MAX_CACHE_TIME - 1);
            // If one fails, stop trying for that session
        } else {
            $this->debug('Fetch successful!');
        }
        return $response;
    }

    /**
     * @param array $pairs
     * @return Player|null
     */
    public function getPlayer($pairs = []) {
        foreach ($pairs as $key => $val) {
            if ($val != null && $val != '') {
                if ($key == KEYS::PLAYER_BY_UNKNOWN ||
                    $key == KEYS::PLAYER_BY_NAME
                ) {
                    return $this->getPlayer([KEYS::PLAYER_BY_UUID => $this->getUUIDFromVar($val)]);
                }
                if ($key == KEYS::PLAYER_BY_UUID) {
                    $val = Utilities::ensureNoDashesUUID($val);
                    if (InputType::getType($val) !== InputType::UUID) continue;

                    $query = ['record.uuid' => (string)$val]; // TODO make sure there's an index here
                    $content = $this->getCacheMongo(COLLECTION_NAMES::PLAYERS, $query);
                    if ($content != null) {
                        $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                        if (time() - $this->getCacheTime(CACHE_TIMES::PLAYER) < $timestamp) {
                            $PLAYER = new Player($content, $this);
                            return $PLAYER;
                        }
                    }

                    $response = $this->fetch(API_REQUESTS::PLAYER, $key, $val);
                    if ($response['success'] == true) {
                        $PLAYER = new Player([
                            'record' => $response['player'],
                            'extra' => $content['extra']
                        ], $this);
                        if (!is_array($PLAYER->getRecord())) {
                            $PLAYER->JSONArray['record'] = [];
                        } // null players fix (valid uuid no hypixel stats)
                        $PLAYER->JSONArray['record']['uuid'] = (string)$val;
                        $PLAYER->handleNew();
                        $this->setCacheMongo(COLLECTION_NAMES::PLAYERS, $query, $PLAYER);
                        return $PLAYER;
                    }
                }
            }
        }
        if ($this->getCacheTime(CACHE_TIMES::PLAYER) < self::MAX_CACHE_TIME) {
            $this->setCacheTime(self::MAX_CACHE_TIME, [CACHE_TIMES::PLAYER]);
            return $this->getPlayer($pairs);
        }
        return null;
    }

    /**
     * get Guild of Player
     * @param array $pairs
     * @return Guild|null
     */
    public function getGuild($pairs = []) {
        foreach ($pairs as $key => $val) {
            if ($val != null && $val != '') {
                if ($key == KEYS::GUILD_BY_PLAYER_UNKNOWN ||
                    $key == KEYS::GUILD_BY_PLAYER_NAME ||
                    $key == KEYS::GUILD_BY_PLAYER_OBJECT
                ) {
                    return $this->getGuild([KEYS::GUILD_BY_PLAYER_UUID => $this->getUUIDFromVar($val)]);
                }

                if ($key == KEYS::GUILD_BY_PLAYER_UUID) {
                    // Check if we have a guild on file with that name
                    $query = ['uuid' => strtolower((string)$val)]; // TODO make sure there's an index here
                    $content = $this->getCacheMongo(COLLECTION_NAMES::GUILDS_UUID, $query);
                    if ($content != null) {
                        $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                        if (time() - $this->getCacheTime() < $timestamp) {
                            return $this->getGuild([KEYS::GUILD_BY_ID => $content['guild']]);
                        }
                    }

                    $val = Utilities::ensureNoDashesUUID($val);
                    if (InputType::getType($val) != InputType::UUID) continue;

                    $response = $this->fetch(API_REQUESTS::FIND_GUILD, $key, $val);
                    if ($response['success'] == true) {
                        $content = ['timestamp' => time(), 'guild' => $response['guild'], 'uuid' => strtolower((string)$val)];
                        $this->setCacheMongo(COLLECTION_NAMES::GUILDS_UUID, $query, $content);
                        return $this->getGuild([KEYS::GUILD_BY_ID => $response['guild']]);
                    }
                }

                if ($key == KEYS::GUILD_BY_NAME) {
                    // Check if we have a guild on file with that name
                    $query = ['extra.name_lower' => strtolower((string)$val)]; // TODO make sure there's an index here
                    $content = $this->getCacheMongo(COLLECTION_NAMES::GUILDS, $query);
                    if ($content != null) {
                        $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                        if (time() - $this->getCacheTime() < $timestamp) {
                            return new Guild($content, $this);
                        }
                    }
                }

                if ($key == KEYS::GUILD_BY_ID) {
                    $query = ['record._id' => (string)$val];
                    $content = $this->getCacheMongo(COLLECTION_NAMES::GUILDS, $query);
                    if ($content != null) {
                        $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                        if (time() - $this->getCacheTime() < $timestamp) {
                            return new Guild($content, $this);
                        }
                    }

                    $response = $this->fetch(API_REQUESTS::GUILD, $key, $val);
                    if ($response['success'] == true) {
                        $GUILD = new Guild([
                            'record' => $response['guild'],
                            'extra' => $content['extra']
                        ], $this);
                        if (!is_array($GUILD->getRecord())) {
                            $GUILD->JSONArray['record'] = [];
                        }
                        $GUILD->JSONArray['record']['_id'] = (string)$val;
                        $GUILD->handleNew();
                        $this->setCacheMongo(COLLECTION_NAMES::GUILDS, $query, $GUILD);
                        return $GUILD;
                    }
                }
            }
        }
        if ($this->getCacheTime(CACHE_TIMES::GUILD) < self::MAX_CACHE_TIME) {
            $this->setCacheTime(self::MAX_CACHE_TIME, [CACHE_TIMES::GUILD]);
            return $this->getGuild($pairs);
        }
        return null;
    }

    /**
     * Get Session of Player
     * @param array $pairs
     * @return Session|null
     */
    public function getSession($pairs = []) {
        foreach ($pairs as $key => $val) {
            if ($val != null && $val != '') {
                if ($key == KEYS::SESSION_BY_PLAYER_OBJECT ||
                    $key == KEYS::SESSION_BY_NAME
                ) {
                    return $this->getSession([KEYS::SESSION_BY_UUID => $this->getUUIDFromVar($val)]);
                }

                if ($key == KEYS::SESSION_BY_UUID) {
                    $query = ['record.uuid' => (string)$val]; // TODO make sure there's an index here
                    $content = $this->getCacheMongo(COLLECTION_NAMES::SESSIONS, $query);
                    if ($content != null) {
                        $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                        if (time() - $this->getCacheTime() < $timestamp) {
                            return new Session($content, $this);
                        }
                    }

                    $response = $this->fetch(API_REQUESTS::SESSION, $key, $val);
                    if ($response['success'] === true) {
                        $SESSION = new Session([
                            'record' => $response['session'],
                            'extra' => $content['extra']
                        ], $this);
                        if (!is_array($SESSION->getRecord())) {
                            $SESSION->JSONArray['record'] = [];
                        }
                        $SESSION->JSONArray['record']['uuid'] = (string)$val;
                        $this->setCacheMongo(COLLECTION_NAMES::SESSIONS, $query, $SESSION);
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
     * @param array $pairs
     * @return FriendsList|null
     */
    public function getFriends($pairs = []) {
        foreach ($pairs as $key => $val) {
            if ($val != null && $val != '') {
                if ($key == KEYS::FRIENDS_BY_PLAYER_OBJECT ||
                    $key == KEYS::FRIENDS_BY_NAME
                ) {
                    return $this->getFriends([KEYS::FRIENDS_BY_UUID => $this->getUUIDFromVar($val)]);
                }


                if ($key == KEYS::FRIENDS_BY_UUID) {
                    $query = ['record.uuid' => (string)$val]; // TODO make sure there's an index here
                    $content = $this->getCacheMongo(COLLECTION_NAMES::FRIENDS, $query);
                    if ($content != null) {
                        $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                        if (time() - $this->getCacheTime() < $timestamp) {
                            return new FriendsList($content, $this);
                        }
                    }

                    $response = $this->fetch(API_REQUESTS::FRIENDS, $key, $val);
                    if ($response['success'] == true) {
                        $FRIENDS = new FriendsList([
                            'record' => ['list' => $response['records']],
                            'extra' => $content['extra']
                        ], $this);
                        $FRIENDS->JSONArray['record']['uuid'] = (string)$val;
                        $FRIENDS->handleNew();
                        $this->setCacheMongo(COLLECTION_NAMES::FRIENDS, $query, $FRIENDS);
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
    public function getBoosters() {
        $filename = $this->options['cache_boosters'];
        $content = $this->getCache($filename);
        if ($content != null) {
            $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
            if (time() - $this->getCacheTime() < $timestamp) {
                return new Boosters($content, $this);
            }
        }

        $response = $this->fetch(API_REQUESTS::BOOSTERS);
        if ($response['success'] == true) {
            $BOOSTERS = new Boosters([
                'record' => $response['boosters'],
                'extra' => $content['extra']
            ], $this);
            $BOOSTERS->setExtra(['filename' => $filename]);
            $BOOSTERS->handleNew();
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
    public function getLeaderboards() {
        $filename = $this->options['cache_leaderboards'];
        $content = $this->getCache($filename);
        if ($content != null) {
            $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
            if (time() - $this->getCacheTime() < $timestamp) {
                return new Leaderboards($content, $this);
            }
        }

        $response = $this->fetch(API_REQUESTS::LEADERBOARDS);
        if ($response['success'] == true) {
            $LEADERBOARDS = new Leaderboards([
                'record' => $response['leaderboards'],
                'extra' => $content['extra']
            ], $this);
            $LEADERBOARDS->setExtra(['filename' => $filename]);
            $LEADERBOARDS->handleNew();
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
    public function getKeyInfo() {
        $query = ['record.key' => (string)$this->getKey()]; // TODO make sure there's an index here
        $content = $this->getCacheMongo(COLLECTION_NAMES::API_KEYS, $query);
        if ($content != null) {
            $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
            if (time() - $this->getCacheTime() < $timestamp) {
                $this->debug('Getting Cached data!');
                return new KeyInfo($content, $this);
            }
        }

        $response = $this->fetch(API_REQUESTS::KEY);
        if ($response['success'] == true) {
            $content = ['timestamp' => time(), 'record' => $response['record']];
            if (!is_array($content['record'])) {
                $content['record'] = [];
            }
            $content['record']['key'] = (string)$this->getKey();
            $this->setCacheMongo(COLLECTION_NAMES::API_KEYS, $query, $content);
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
    public function getFileContent($filename) {
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
    public function setFileContent($filename, $content) {
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
    public function getCacheFileName($input) {
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
    public function getCache($filename) {
        $content = $this->getFileContent($filename);
        if ($content == null) {
            return null;
        }
        $content = json_decode($content, true);
        if ($content == null) {
            return null;
        }
        if (!array_key_exists('extra', $content)) {
            $content['extra'] = [];
        }
        return $content;
    }

    /**
     * @param $collection
     * @param $query
     * @return array|null
     */
    public function getCacheMongo($collection, $query) {
        /** @noinspection PhpUndefinedFieldInspection */
        $content = $this->MONGO_CLIENT->HypixelAPI->createCollection($collection)->findOne($query);
        if ($content == null) {
            return null;
        }
        if (!array_key_exists('extra', $content)) {
            $content['extra'] = [];
        }
        return $content;
    }

    /**
     * @param               $filename
     * @param HypixelObject $obj
     */
    public function setCache($filename, HypixelObject $obj) {
        $content = json_encode($obj->getRaw());
        $this->setFileContent($filename, $content);
    }

    /**
     * @param $collection
     * @param $query
     * @param $obj
     */
    public function setCacheMongo($collection, $query, $obj) {
        if ($obj instanceof HypixelObject) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->MONGO_CLIENT->HypixelAPI->createCollection($collection)->update($query, $obj->getRaw(), ['upsert' => true]);
        } else {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->MONGO_CLIENT->HypixelAPI->createCollection($collection)->update($query, $obj, ['upsert' => true]);
        }
    }

    /**
     * Function to get and cache UUID from username.
     * @param string $username
     * @param string $url
     *
     * @return string|bool
     */
    public function getUUID($username, $url = 'https://api.mojang.com/users/profiles/minecraft/%s') {
        $query = ['name_lowercase' => strtolower($username)];
        $content = $this->getCacheMongo(COLLECTION_NAMES::PLAYER_UUID, $query);
        if ($content != null) {
            $CACHE_TIME = $this->getCacheTime(CACHE_TIMES::UUID);
            if (!isset($content['uuid']) || $content['uuid'] == null || $content['uuid'] == '') {
                $CACHE_TIME = $this->getCacheTime(CACHE_TIMES::UUID_NOT_FOUND);
                // allow for faster fail over when uuid is null/not found
            }
            $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
            $diff = time() - $CACHE_TIME - $timestamp;
            $this->debug('Found NAME match in PLAYER_UUID! \'' . abs($diff) . '\'');
            if ($diff < 0) {
                return $content['uuid'];
            }
        }

        if ($this->getCacheTime(CACHE_TIMES::UUID) == self::MAX_CACHE_TIME) {
            $query = ['record.playername' => strtolower($username)];
            $content = $this->getCacheMongo(COLLECTION_NAMES::PLAYERS, $query);
            if ($content != null) {
                $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                $diff = time() - $this->getCacheTime(CACHE_TIMES::UUID) - $timestamp;
                if ($diff < 0) {
                    $this->debug('Found NAME match in PLAYERS! \'' . strtolower($username) . '\' Cache valid! ' . abs($diff));
                    return $content['record']['uuid'];
                } else {
                    $this->debug('Found NAME match in PLAYERS! \'' . strtolower($username) . '\' Cache expired! ' . abs($diff));
                }
            }
        }

        $uuidURL = sprintf($url, $username);
        $response = $this->getUrlContents($uuidURL);
        if (isset($response['id'])) {
            $this->debug('UUID for username fetched!');
            $content = [
                'timestamp' => time(),
                'name_lowercase' => strtolower((string)$username),
                'uuid' => Utilities::ensureNoDashesUUID($response['id'])
            ];
            if ($content['uuid'] == '' || $content['uuid'] == null) {
                $this->setCacheMongo(COLLECTION_NAMES::PLAYER_UUID, ['name_lowercase' => strtolower($username)], ['$set' => [['timestamp' => time()]]]);
            } else {
                $this->setCacheMongo(COLLECTION_NAMES::PLAYER_UUID, ['name_lowercase' => strtolower($username)], $content);
            }
            $this->debug($username . ' => ' . $content['uuid']);
            return $content['uuid'];
        }

        if ($this->getCacheTime(CACHE_TIMES::UUID) < self::MAX_CACHE_TIME) {
            $this->setCacheTime(self::MAX_CACHE_TIME, [CACHE_TIMES::UUID]);
            return $this->getUUID($username, $url);
        }
        $this->debug('unable to fetch UUID!', false);
        return false;
    }

    public function getUUIDFromVar($value) {
        $uuid = null;
        $type = InputType::getType($value);
        if ($type != null) {
            if ($type == InputType::USERNAME) {
                $this->debug('Input is username, fetching UUID.', false);
                $uuid = $this->getUUID((string)$value);
            } else if ($type == InputType::UUID) {
                $this->debug('Input is UUID.', false);
                $uuid = $value;
            } else if ($type == InputType::PLAYER_OBJECT) {
                $this->debug('Input is Player Object.', false);
                /** @var Player $value */
                $uuid = $value->getUUID();
            }
            if ($uuid === false) return null;
        }
        return $uuid;
    }

    /**
     * Get the last error and status code associated with the last cURL fetch.
     * @return null|array
     */
    public function getUrlError() {
        return end($this->getUrlErrors);
    }

    /**
     * Get all errors associated to cURL fetches in the current session.
     * @return array
     */
    public function getUrlErrors() {
        return $this->getUrlErrors;
    }

}

class Utilities {

    public static function ensureNoDashesUUID($uuid) {
        return str_replace("-", "", $uuid);
    }

    public static function ensureDashedUUID($uuid) {
        if (strpos($uuid, "-")) {
            if (strlen($uuid) == 32) {
                return $uuid;
            }
            $uuid = Utilities::ensureNoDashesUUID($uuid);
        }
        return substr($uuid, 0, 8) . "-" . substr($uuid, 8, 12) . substr($uuid, 12, 16) . "-" . substr($uuid, 16, 20) . "-" . substr($uuid, 20, 32);
    }

    public static function isUUID($input) {
        return is_string($input) && (strlen($input) == 32 || strlen($input) == 28);
    }

    const COLOR_CHAR = '§';
    const MC_COLORS = [
        '0' => '#000000',
        '1' => '#0000AA',
        '2' => '#008000',
        '3' => '#00AAAA',
        '4' => '#AA0000',
        '5' => '#AA00AA',
        '6' => '#FFAA00',
        '7' => '#AAAAAA',
        '8' => '#555555',
        '9' => '#5555FF',
        'a' => '#3CE63C',
        'b' => '#3CE6E6',
        'c' => '#FF5555',
        'd' => '#FF55FF',
        'e' => '#FFFF55',
        'f' => '#FFFFFF'
    ];

    const MC_COLORNAME = [
        "BLACK" => '§0',
        "DARK_BLUE" => '§1',
        "DARK_GREEN" => '§2',
        "DARK_AQUA" => '§3',
        "DARK_RED" => '§4',
        "DARK_PURPLE" => '§5',
        "GOLD" => '§6',
        "GRAY" => '§7',
        "DARK_GRAY" => '§8',
        "BLUE" => '§9',
        "GREEN" => '§a',
        "AQUA" => '§b',
        "RED" => '§c',
        "LIGHT_PURPLE" => '§d',
        "YELLOW" => '§e',
        "WHITE" => '§f',
        "MAGIC" => '§k',
        "BOLD" => '§l',
        "STRIKETHROUGH" => '§m',
        "UNDERLINE" => '§n',
        "ITALIC" => '§o',
        "RESET" => '§r'
    ];

    /**
     * Parses MC encoded colors to HTML
     * @param $string
     * @return string
     */
    public static function parseColors($string) {
        if ($string == null) return null;

        if (strpos($string, Utilities::COLOR_CHAR) === false) {
            return $string;
        }
        $d = explode(Utilities::COLOR_CHAR, $string);
        $out = '';
        foreach ($d as $part) {
            if (strlen($part) == 0) continue;
            $out = $out . "<span style='color:" . Utilities::MC_COLORS[substr($part, 0, 1)] . "'>" . substr($part, 1) . "</span>";
        }
        return $out;
    }

    /**
     * Parses MC encoded colors to HTML
     * @param $string
     * @return string
     */
    public static function stripColors($string) {
        if ($string == null) return null;
        if (strpos($string, Utilities::COLOR_CHAR) === false) {
            return $string;
        }
        $d = explode(Utilities::COLOR_CHAR, $string);
        $out = '';
        foreach ($d as $part) {
            $out .= substr($part, 1);
        }
        return $out;
    }

}

class CACHE_TIMES {
    const OVERALL = 'overall';

    const PLAYER = 'player';
    const UUID = 'uuid';
    const UUID_NOT_FOUND = 'uuid_not_found';

    const GUILD = 'guild';
    const GUILD_NOT_FOUND = 'guild_not_found';

    // everything else just uses OVERALL

    public static function getAllTypes() {
        $obj = new \ReflectionClass ('\HypixelPHP\CACHE_TIMES');
        return $obj->getConstants();
    }
}

class API_REQUESTS {
    const PLAYER = 'player';

    const GUILD = 'guild';
    const FIND_GUILD = 'findGuild';

    const FRIENDS = 'friends';
    const BOOSTERS = 'boosters';
    const LEADERBOARDS = 'leaderboards';
    const SESSION = 'session';
    const KEY = 'key';
}

class KEYS {
    const PLAYER_BY_NAME = 'name';
    const PLAYER_BY_UUID = 'uuid';
    const PLAYER_BY_UNKNOWN = 'unknown';

    public static function getPlayerKeys() {
        return [
            KEYS::PLAYER_BY_NAME,
            KEYS::PLAYER_BY_UUID,
            KEYS::PLAYER_BY_UNKNOWN
        ];
    }

    const GUILD_BY_NAME = 'byName'; // via guild name
    const GUILD_BY_PLAYER_UUID = 'byUuid'; // via player uuid
    const GUILD_BY_PLAYER_NAME = 'byPlayer'; // via player name
    const GUILD_BY_PLAYER_UNKNOWN = 'unknown';
    const GUILD_BY_PLAYER_OBJECT = 'player'; // via Player Object, gets uuid
    const GUILD_BY_ID = 'id'; // via guild id

    public static function getGuildKeys() {
        return [
            KEYS::GUILD_BY_NAME,
            KEYS::GUILD_BY_PLAYER_UUID,
            KEYS::GUILD_BY_PLAYER_NAME,
            KEYS::GUILD_BY_PLAYER_UNKNOWN,
            KEYS::GUILD_BY_PLAYER_OBJECT,
            KEYS::GUILD_BY_ID
        ];
    }

    const FRIENDS_BY_NAME = 'name'; // via player uuid
    const FRIENDS_BY_UUID = 'uuid'; // via player name
    const FRIENDS_BY_PLAYER_OBJECT = 'player'; // via Player Object, gets uuid

    public static function getFriendsKeys() {
        return [
            KEYS::FRIENDS_BY_NAME,
            KEYS::FRIENDS_BY_UUID,
            KEYS::FRIENDS_BY_PLAYER_OBJECT
        ];
    }

    const SESSION_BY_NAME = 'name'; // via player uuid
    const SESSION_BY_UUID = 'uuid'; // via player name
    const SESSION_BY_PLAYER_OBJECT = 'player'; // via Player Object, gets uuid

    public static function getSessionKeys() {
        return [
            KEYS::SESSION_BY_NAME,
            KEYS::SESSION_BY_UUID,
            KEYS::SESSION_BY_PLAYER_OBJECT
        ];
    }
}

class COLLECTION_NAMES {
    const PLAYERS = 'players';
    const PLAYER_UUID = 'player_uuid';

    const FRIENDS = 'friends';

    const GUILDS = 'guilds';
    const GUILDS_UUID = 'guilds_uuid';

    const SESSIONS = 'sessions';
    const API_KEYS = 'api_keys';
}

class InputType {
    const UUID = 0;
    const USERNAME = 1;
    const PLAYER_OBJECT = 2;

    /**
     * Determine input type
     * @param $input
     *
     * @return int|null
     */
    public static function getType($input) {
        if ($input instanceof Player) return InputType::PLAYER_OBJECT;
        if (Utilities::isUUID($input)) return InputType::UUID;
        if (is_string($input) && strlen($input) <= 16) return InputType::USERNAME;
        return null;
    }
}

/**
 * Class HypixelObject
 *
 * @package HypixelPHP
 */
class HypixelObject {
    public $JSONArray;
    public $api;

    /**
     * @param            $json
     * @param HypixelPHP $api
     */
    public function __construct($json, HypixelPHP $api) {
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

    public function handleNew() {

    }

    /**
     * @return array
     */
    public function getRaw() {
        return $this->JSONArray;
    }

    public function getRecord() {
        return $this->JSONArray['record'];
    }

    /**
     * @param      $key
     * @param bool $implicit
     * @param null $default
     *
     * @param string $delimiter
     * @return array|null
     */
    public function get($key, $implicit = false, $default = null, $delimiter = '.') {
        if (!array_key_exists('record', $this->JSONArray)) return $default;
        if (!is_array($this->JSONArray['record'])) return $default;
        if (!$implicit) {
            return $this->getRecursiveValue($this->JSONArray['record'], $key, $default, $delimiter);
        }
        return in_array($key, array_keys($this->JSONArray['record'])) ? $this->JSONArray['record'][$key] : $default;
    }

    public function getRecursiveValue($array, $key, $default = null, $delimiter = '.') {
        $return = $array;
        foreach (explode($delimiter, $key) as $split) {
            $return = isset($return[$split]) ? $return[$split] : $default;
        }
        return $return ? $return : $default;
    }

    /**
     * @param $key
     * @param int $default
     * @return int
     */
    public function getInt($key, $default = 0) {
        return $this->get($key, false, $default);
    }

    /**
     * @param $key
     * @return array
     */
    public function getArray($key) {
        return $this->get($key, false, []);
    }

    /**
     * @return array|null
     */
    public function getID() {
        return $this->get('_id');
    }

    /**
     * @return bool
     */
    public function isCached() {
        return abs(time() - $this->getCachedTime()) > 1;
    }

    /**
     * @param int $extra
     * @return bool
     */
    public function isCacheExpired($extra = -1) {
        return time() - ($extra == -1 ? $this->api->getCacheTime(null, true) : $extra) > $this->getCachedTime();
    }

    public function getCachedTime() {
        return $this->JSONArray['timestamp'];
    }

    /**
     * @param $input
     */
    public function setExtra($input) {
        $anyChange = false;
        foreach ($input as $key => $val) {
            if (array_key_exists($key, $this->JSONArray['extra'])) {
                if ($val == null) {
                    unset($this->JSONArray['extra'][$key]);
                    $anyChange = true;
                    continue;
                } else if ($this->JSONArray['extra'][$key] == $val) {
                    continue;
                }
            }
            $this->api->debug('Extra \'' . $key . '\' set to ' . json_encode($val));
            $this->JSONArray['extra'][$key] = $val;
            $anyChange = true;
        }
        if ($anyChange) {
            $this->saveCache();
        }
    }

    public function getExtra($key = null, $implicit = false, $default = null, $delimiter = '.') {
        if ($key != null) {
            if (!array_key_exists('extra', $this->JSONArray)) return $default;
            if (!is_array($this->JSONArray['extra'])) return $default;
            if (!$implicit) {
                return $this->getRecursiveValue($this->JSONArray['extra'], $key, $default, $delimiter);
            }
            return in_array($key, array_keys($this->JSONArray['extra'])) ? $this->JSONArray['extra'][$key] : $default;
        }
        return $this->JSONArray['extra'];
    }

    public function saveCache() {
        if ($this instanceof Player) {
            $this->api->setCacheMongo(COLLECTION_NAMES::PLAYERS, ['record.uuid' => $this->getUUID()], $this);
            return;
        } elseif ($this instanceof Guild) {
            $this->api->setCacheMongo(COLLECTION_NAMES::GUILDS, ['record._id' => $this->getID()], $this);
            return;
        } elseif ($this instanceof FriendsList) {
            $this->api->setCacheMongo(COLLECTION_NAMES::FRIENDS, ['record.uuid' => $this->getUUID()], $this);
            return;
        } elseif ($this instanceof Session) {
            $this->api->setCacheMongo(COLLECTION_NAMES::SESSIONS, ['record.uuid' => $this->getUUID()], $this);
            return;
        } elseif ($this instanceof KeyInfo) {
            $this->api->setCacheMongo(COLLECTION_NAMES::API_KEYS, ['record.key' => $this->getKey()], $this);
            return;
        }
        if (array_key_exists('filename', $this->getExtra())) {
            $this->api->debug('Saving cache file', false);
            $this->api->setCache($this->JSONArray['extra']['filename'], $this);
        }
    }

    public function isValid() {
        return true;
    }
}

/**
 * Class KeyInfo
 *
 * @package HypixelPHP
 */
class KeyInfo extends HypixelObject {

    public function getKey() {
        return $this->get('key');
    }

}

/**
 * Class Player
 *
 * @package HypixelPHP
 */
class Player extends HypixelObject {
    private $guild, $friends, $session;

    public function handleNew() {
        $this->getAchievementPoints(true);
    }

    /**
     * get Session of Player
     * @return Session|null
     */
    public function getSession() {
        if ($this->session == null) {
            $this->session = $this->api->getSession([KEYS::SESSION_BY_PLAYER_OBJECT => $this]);
        }
        return $this->session;
    }

    /**
     * get Friends of Player
     * @return FriendsList|null
     */
    public function getFriends() {
        if ($this->friends == null) {
            $this->friends = $this->api->getFriends([KEYS::FRIENDS_BY_PLAYER_OBJECT => $this]);
        }
        return $this->friends;
    }

    /**
     * get Boosters of Player
     * @return Booster[]
     */
    public function getBoosters() {
        $BOOSTERS = $this->api->getBoosters();
        if ($BOOSTERS != null) {
            return $BOOSTERS->getBoosters($this->getUUID());
        }
        return [];
    }

    /**
     * get Guild of Player
     * @return Guild|null
     */
    public function getGuild() {
        if ($this->guild == null) {
            $this->guild = $this->api->getGuild([KEYS::GUILD_BY_PLAYER_OBJECT => $this]);
        }
        return $this->guild;
    }

    /**
     * @return string
     */
    public function getName() {
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
     * get Formatted name of Player
     *
     * @param bool $prefix
     * @param bool $guildTag
     * @param bool $parseColors
     * @return string
     */
    public function getFormattedName($prefix = true, $guildTag = false, $parseColors = true) {
        $rank = $this->getRank(false);
        $out = $rank->getColor() . $this->getName();
        if ($prefix) {
            $out = ($this->getPrefix() != null ? $this->getPrefix() : $rank->getPrefix($this)) . ' ' . $this->getName();
        }
        if ($guildTag) {
            $out .= $this->getGuildTag() != null ? ' §7[' . $this->getGuildTag() . ']' : '';
        }
        if ($parseColors) {
            $outStr = Utilities::parseColors($out);
        } else {
            $outStr = Utilities::stripColors($out);
        }
        return $outStr;
    }

    public function getRawFormattedName($prefix = true, $guildTag = false) {
        $rank = $this->getRank(false);
        $out = $rank->getColor() . $this->getName();
        if ($prefix) {
            $out = ($this->getPrefix() != null ? $this->getPrefix() : $rank->getPrefix()) . ' ' . $this->getName();
        }
        if ($guildTag) {
            $out .= $this->getGuildTag() != null ? ' §7[' . $this->getGuildTag() . ']' : '';
        }
        return $out;
    }

    /**
     * Get player Guild Tag, null if no guild/tag
     *
     * @return string|null
     */
    public function getGuildTag() {
        $guild = $this->getGuild();
        if ($guild != null) {
            if ($guild->canTag()) {
                return $guild->getTag();
            }
        }
        return null;
    }

    /**
     * Get player UUID
     *
     * @return string
     */
    public function getUUID() {
        return $this->get('uuid');
    }

    /**
     * Get the Stats object for the player
     *
     * @return Stats
     */
    public function getStats() {
        return new Stats($this->getArray('stats'), $this->api);
    }

    /**
     * Check if player has a PreEULA rank
     *
     * @return bool
     */
    public function isPreEULA() {
        return $this->get('packageRank', true, null) != null;
    }

    /**
     * Get Level of player
     *
     * @param bool $zeroBased
     * @return int
     */
    public function getLevel($zeroBased = false) {
        return $this->getInt('networkLevel') + (!$zeroBased ? 1 : 0);
    }

    /**
     * @return string|null
     */
    public function getPrefix() {
        return $this->get('prefix', false, null);
    }

    /**
     * @return bool
     */
    public function isStaff() {
        $rank = $this->get('rank', true, 'NORMAL');
        if ($rank == 'NORMAL') {
            return false;
        }
        return true;
    }

    /**
     * get Current Multiplier, accounts for level and Pre-EULA rank
     * @return int
     */
    public function getMultiplier() {
        if ($this->getRank(false)->getId() == RankTypes::YOUTUBER) {
            return RankTypes::fromID(RankTypes::YOUTUBER)->getMultiplier();
        }
        $pre = $this->getRank(true, ['packageRank']); // only old rank matters
        $eulaMultiplier = $pre != null ? $pre->getMultiplier() : 1;
        $levelMultiplier = min(floor($this->getLevel() / 25) + 1, 6);
        return ($eulaMultiplier > $levelMultiplier) ? $eulaMultiplier : $levelMultiplier;
    }

    /**
     * get Rank
     * @param bool $package
     * @param array $rankKeys
     * @return Rank
     */
    public function getRank($package = true, $rankKeys = ['newPackageRank', 'packageRank']) {
        $returnRank = null;
        if ($package) {
            $returnRank = null;
            foreach ($rankKeys as $key) {
                $rank = RankTypes::fromName($this->get($key));
                if ($rank != null) {
                    if ($returnRank == null) $returnRank = $rank;
                    /** @var $rank \HypixelPHP\Rank */
                    /** @var $returnRank \HypixelPHP\Rank */
                    if ($rank->getId() > $returnRank->getId()) {
                        $returnRank = $rank;
                    }
                }
            }
        } else {
            if (!$this->isStaff()) return $this->getRank(true);
            $returnRank = RankTypes::fromName($this->get('rank'));
        }
        if ($returnRank == null) {
            $returnRank = RankTypes::fromID(RankTypes::NON_DONOR);
        }
        return $returnRank;
    }

    /**
     * get Player achievement points
     * @param bool $force_update
     * @return int
     */
    public function getAchievementPoints($force_update = false) {
        if (!$force_update) {
            return $this->getExtra('achievementPoints', false, 0);
        }

        $isLogging = $this->api->getOptions()['logging'];
        $this->api->set(['logging' => false]);
        $achievements = $this->api->getFileContent($this->api->getOptions()['achievements_file']);
        if ($achievements == null) {
            return 0;
        }
        $achievements = json_decode($achievements, true)['achievements'];
        $total = 0;
        $oneTime = $this->get('achievementsOneTime', false, []);
        $this->api->debug('Starting OneTime Achievements');
        foreach ($oneTime as $dbName) {
            if (!is_string($dbName)) continue;
            $game = strtolower(substr($dbName, 0, strpos($dbName, "_")));
            $dbName = strtoupper(substr($dbName, strpos($dbName, "_") + 1));
            if (!in_array($game, array_keys($achievements))) {
                continue;
            }
            $this->api->debug('Achievement: ' . strtoupper(substr($dbName, strpos($dbName, "_"))));
            if (in_array($dbName, array_keys($achievements[$game]['one_time']))) {
                $this->api->debug('Achievement: ' . $dbName . ' - ' . $achievements[$game]['one_time'][$dbName]['points']);
                $total += $achievements[$game]['one_time'][$dbName]['points'];
            }
        }
        $tiered = $this->get('achievements', false, []);
        $this->api->debug('Starting Tiered Achievements');
        foreach ($tiered as $dbName => $value) {
            $game = strtolower(substr($dbName, 0, strpos($dbName, "_")));
            $dbName = strtoupper(substr($dbName, strpos($dbName, "_") + 1));
            if (!in_array($game, array_keys($achievements))) {
                continue;
            }
            $this->api->debug('Achievement: ' . $dbName);
            if (in_array($dbName, array_keys($achievements[$game]['tiered']))) {
                $tierTotal = 0;
                foreach ($achievements[$game]['tiered'][$dbName]['tiers'] as $tier) {
                    if ($value >= $tier['amount']) {
                        $this->api->debug('Tier: ' . $tier['amount'] . ' - ' . $tier['points']);
                        $tierTotal += $tier['points'];
                    }
                }
                $total += $tierTotal;
            }
        }
        $this->api->set(['logging' => $isLogging]);
        $this->setExtra(['achievementPoints' => $total, 'achievementTimestamp' => time()]);
        return $total;
    }

    /**
     * @param $key
     * @param bool $implicit
     * @param null $default
     *
     * @param string $delimiter
     * @return array|float|int|mixed|null
     */
    public function get($key, $implicit = false, $default = null, $delimiter = '.') {
        return parent::get($key, $implicit, $default, $delimiter);
    }

    public function isValid() {
        return is_array($this->JSONArray['record']) && sizeof($this->JSONArray['record']) > 1;
    }
}

class RankTypes {
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
    public static function fromID($id) {
        switch ($id) {
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

    public static function fromName($db) {
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
    public static function getAllTypes() {
        $obj = new \ReflectionClass ('\HypixelPHP\RankTypes');
        return $obj->getConstants();
    }
}

class Rank {
    private $name, $id, $options, $staff;

    /**
     * @param $id
     * @param $name
     * @param $options
     * @param bool $staff
     */
    public function __construct($id, $name, $options, $staff = false) {
        $this->id = $id;
        $this->name = $name;
        $this->options = $options;
        $this->staff = $staff;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getCleanName() {
        if ($this->name == 'NON_DONOR' || $this->name == 'NONE') return 'DEFAULT';
        return str_replace("_", ' ', str_replace('_PLUS', '+', $this->name));
    }

    public function getOptions() {
        return $this->options;
    }

    public function isStaff() {
        return $this->staff;
    }

    public function getPrefix(Player $player) {
        if ($this->name == 'MVP_PLUS' && $player->get("rankPlusColor") != null) {
            return '§b[MVP' . Utilities::MC_COLORNAME[$player->get("rankPlusColor")] . '+§b]';
        }
        return isset($this->options['prefix']) ? $this->options['prefix'] : null;
    }

    public function getColor() {
        return isset($this->options['color']) ? $this->options['color'] : null;
    }

    public function getMultiplier() {
        return isset($this->options['eulaMultiplier']) ? $this->options['eulaMultiplier'] : 1;
    }

    public function __toString() {
        return json_encode([$this->name => $this->options]);
    }
}

/**
 * Class Stats
 *
 * @package HypixelPHP
 */
class Stats extends HypixelObject {
    /**
     * @param            $json
     * @param HypixelPHP $api
     */
    public function __construct($json, HypixelPHP $api) {
        parent::__construct(['record' => $json], $api);
    }

    /**
     * @param $game
     *
     * @return GameStats
     */
    public function getGame($game) {
        $game = $this->get($game, true, null);
        return new GameStats($game, $this->api);
    }

    public function getGameFromID($id) {
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
class GameStats extends HypixelObject {
    /**
     * @param            $json
     * @param HypixelPHP $api
     */
    public function __construct($json, HypixelPHP $api) {
        parent::__construct(['record' => $json], $api);
    }

    /**
     * @return array|null
     */
    public function getPackages() {
        return $this->getArray('packages');
    }

    /**
     * @param $package
     * @return bool
     */
    public function hasPackage($package) {
        return in_array($package, $this->getArray('packages'));
    }

    /**
     * @return int
     */
    public function getCoins() {
        return $this->getInt('coins');
    }

    /**
     * @param $stat
     * @return array|null|string|int
     */
    public function getWeeklyStat($stat) {
        return $this->get($stat . '_' . TimeUtils::getWeeklyOscillation());
    }

    /**
     * @param $stat
     * @return array|null|string|int
     */
    public function getMonthlyStat($stat) {
        return $this->get($stat . '_' . TimeUtils::getMonthlyOscillation());
    }
}

/**
 * Class Session
 *
 * @package HypixelPHP
 */
class Session extends HypixelObject {
    /**
     * @return array
     */
    public function getPlayers() {
        return $this->getArray('players');
    }

    /**
     * @return array|null
     */
    public function getGameType() {
        return $this->get('gameType', true);
    }

    /**
     * @return string
     */
    public function getServer() {
        return $this->get('server', true);
    }

    public function getUUID() {
        return $this->get('uuid', true);
    }

    public function getPlayer() {
        $UUID = $this->getUUID();
        if ($UUID != null) {
            return $this->api->getPlayer([KEYS::PLAYER_BY_UUID => $UUID]);
        }
        return null;
    }
}

/**
 * Class FriendsList
 *
 * @package HypixelPHP
 */
class FriendsList extends HypixelObject {
    private $LIST;

    public function getUUID() {
        return $this->get("uuid");
    }

    /**
     * @return Friend[]
     */
    public function getList() {
        if ($this->LIST == null) {
            $this->LIST = [];
            foreach ($this->getRawList() as $f) {
                array_push($this->LIST, new Friend($f, $this->api, $this->getUUID()));
            }
        }
        return $this->LIST;
    }

    public function getRawList() {
        return $this->get('list', true, []);
    }

    public function getPlayer() {
        if (isset($this->JSONArray['uuid'])) {
            return $this->api->getPlayer([KEYS::PLAYER_BY_UUID => $this->getUUID()]);
        }
        return null;
    }
}

class Friend extends HypixelObject {
    public $UUID_PLAYER;

    public function __construct($FRIEND_OBJ, $API, $UUID_PLAYER) {
        parent::__construct($FRIEND_OBJ, $API);
        $this->UUID_PLAYER = $UUID_PLAYER;
    }

    /**
     * Returns whether or not the Player
     * received the friend request
     *
     * @return bool
     */
    public function wasReceiver() {
        return $this->JSONArray['uuidReceiver'] == $this->UUID_PLAYER;
    }

    public function wasSender() {
        return !$this->wasReceiver();
    }

    public function getOtherPlayer() {
        if ($this->wasReceiver()) {
            return $this->api->getPlayer([KEYS::PLAYER_BY_UUID => $this->JSONArray['uuidSender']]);
        } else {
            return $this->api->getPlayer([KEYS::PLAYER_BY_UUID => $this->JSONArray['uuidReceiver']]);
        }
    }

    public function getSince() {
        return $this->JSONArray['started'];
    }
}

/**
 * Class Guild
 *
 * @package HypixelPHP
 */
class Guild extends HypixelObject {

    public function handleNew() {
        $this->setExtra(['name_lower' => strtolower($this->getName())]); // add lowercase name for faster case insensitive lookup
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->get('name', true, '');
    }

    /**
     * @return bool
     */
    public function canTag() {
        return $this->get('canTag', true, false);
    }

    /**
     * @return string
     */
    public function getTag() {
        return Utilities::parseColors($this->get('tag', true, ''));
    }

    /**
     * @return int
     */
    public function getCoins() {
        return $this->getInt('coins');
    }

    /**
     * @return MemberList
     */
    public function getMemberList() {
        return new MemberList($this->get('members'), $this->api);
    }

    /**
     * @return int
     */
    public function getMaxMembers() {
        $total = 25;
        $level = $this->getInt('memberSizeLevel', -1);
        if ($level >= 0) {
            $total += 5 * $level;
        }
        return $total;
    }

    /**
     * @return int
     */
    public function getMemberCount() {
        return $this->getMemberList()->getMemberCount();
    }

    /**
     * get coin history of Guild or Player in Guild
     * @return array
     */
    public function getGuildCoinHistory() {
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
class MemberList extends HypixelObject {
    private $list;
    private $count;

    /**
     * @param            $json
     * @param HypixelPHP $api
     */
    public function __construct($json, $api) {
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
     * @return array[string]GuildMember
     */
    public function getList() {
        return $this->list;
    }

    /**
     * @return int
     */
    public function getMemberCount() {
        return $this->count;
    }
}

/**
 * Class GuildMember
 *
 * @package HypixelPHP
 */
class GuildMember {
    private $coinHistory;
    private $uuid, $name;
    private $joined;
    private $api;

    /**
     * @param $member
     * @param HypixelPHP $api
     */
    public function __construct($member, $api) {
        if (isset($member['coinHistory'])) {
            $this->coinHistory = $member['coinHistory'];
        }
        if (isset($member['uuid'])) {
            $this->uuid = $member['uuid'];
        }
        if (isset($member['name'])) {
            $this->name = $member['name'];
        }
        if (isset($member['joined'])) {
            $this->joined = $member['joined'];
        }
        $this->api = $api;
    }

    /**
     * @return Player
     */
    public function getPlayer() {
        if (isset($this->uuid)) {
            return $this->api->getPlayer([KEYS::PLAYER_BY_UUID => $this->uuid]);
        } else if (isset($this->name)) {
            return $this->api->getPlayer([KEYS::PLAYER_BY_NAME => $this->name]);
        }
        return null;
    }

    /**
     * @return array
     */
    public function getCoinHistory() {
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
class GameTypes {
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
    const SUPER_SMASH = 24;
    const GINGERBREAD = 25;
    const HOUSING = 26;
    const SKYWARS = 51;
    const TRUE_COMBAT = 52;

    /**
     * @param $id
     *
     * @return GameType|null
     */
    public static function fromID($id) {
        switch ($id) {
            case GameTypes::QUAKE:
                return new GameType('Quake', 'Quake', 'Quake', GameTypes::QUAKE);
            case GameTypes::WALLS:
                return new GameType('Walls', 'Walls', 'Walls', GameTypes::WALLS);
            case GameTypes::PAINTBALL:
                return new GameType('Paintball', 'Paintball', 'Paintball', GameTypes::PAINTBALL);
            case GameTypes::HUNGERGAMES:
                return new GameType('HungerGames', 'Blitz Survival Games', 'BSG', GameTypes::HUNGERGAMES);
            case GameTypes::TNTGAMES:
                return new GameType('TNTGames', 'TNT Games', 'TNT Games', GameTypes::TNTGAMES);
            case GameTypes::VAMPIREZ:
                return new GameType('VampireZ', 'VampireZ', 'VampireZ', GameTypes::VAMPIREZ);
            case GameTypes::WALLS3:
                return new GameType('Walls3', 'Mega Walls', 'MW', GameTypes::WALLS3);
            case GameTypes::ARCADE:
                return new GameType('Arcade', 'Arcade', 'Arcade', GameTypes::ARCADE);
            case GameTypes::ARENA:
                return new GameType('Arena', 'Arena Brawl', 'Arena', GameTypes::ARENA);
            case GameTypes::UHC:
                return new GameType('UHC', 'UHC Champions', 'UHC', GameTypes::UHC);
            case GameTypes::MCGO:
                return new GameType('MCGO', 'Cops and Crims', 'CaC', GameTypes::MCGO);
            case GameTypes::BATTLEGROUND:
                return new GameType('Battleground', 'Warlords', 'Warlords', GameTypes::BATTLEGROUND);
            case GameTypes::SUPER_SMASH:
                return new GameType('SuperSmash', 'Smash Heroes', 'Smash Heroes', GameTypes::SUPER_SMASH);
            case GameTypes::GINGERBREAD:
                return new GameType('GingerBread', 'Turbo Kart Racers', 'TKR', GameTypes::GINGERBREAD);
            case GameTypes::HOUSING:
                return new GameType('Housing', 'Housing', 'Housing', GameTypes::HOUSING, false);
            case GameTypes::SKYWARS:
                return new GameType('SkyWars', 'SkyWars', 'SkyWars', GameTypes::SKYWARS);
            case GameTypes::TRUE_COMBAT:
                return new GameType('TrueCombat', 'Crazy Walls', 'Crazy Walls', GameTypes::TRUE_COMBAT);
            default:
                return null;
        }
    }

    public static function fromDbName($db) {
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
    public static function getAllTypes() {
        $obj = new \ReflectionClass ('\HypixelPHP\GameTypes');
        return $obj->getConstants();
    }
}

/**
 * Class GameType
 *
 * @package HypixelPHP
 */
class GameType {
    private $db, $name, $short, $id, $boosters;

    /**
     * @param $db
     * @param $name
     * @param $short
     * @param $id
     * @param bool $boosters
     */
    public function __construct($db, $name, $short, $id, $boosters = true) {
        $this->db = $db;
        $this->name = $name;
        $this->short = $short;
        $this->id = $id;
        $this->boosters = $boosters;
    }

    public function getDb() {
        return $this->db;
    }

    public function getName() {
        return $this->name;
    }

    public function getShort() {
        return $this->short;
    }

    public function getId() {
        return $this->id;
    }

    public function hasBoosters() {
        return $this->boosters;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'short' => $this->short,
            'db' => $this->db
        ];
    }
}

/**
 * Class Boosters
 *
 * @package HypixelPHP
 */
class Boosters extends HypixelObject {
    /**
     * @param int $gameType
     * @param int $max
     *
     * @return array
     */
    public function getQueue($gameType, $max = 999) {
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
     * @param $player
     * @return Booster[]
     */
    public function getBoosters($player) {
        $boosters = [];
        foreach ($this->JSONArray['record'] as $boosterInfo) {
            if (isset($boosterInfo['purchaserUuid'])) {
                if ($boosterInfo['purchaserUuid'] == $player) {
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
class Booster {
    private $info;
    private $api;

    /**
     * @param            $info
     * @param HypixelPHP $api
     */
    public function __construct($info, HypixelPHP $api) {
        $this->info = $info;
        $this->api = $api;
    }

    /**
     * @return Player
     */
    public function getOwner() {
        $oldTime = $this->api->getCacheTime();
        $this->api->setCacheTime(HypixelPHP::MAX_CACHE_TIME - 1);
        $player = $this->api->getPlayer([
            KEYS::PLAYER_BY_NAME => (isset($this->info['purchaser']) ? $this->info['purchaser'] : null),
            KEYS::PLAYER_BY_UUID => (isset($this->info['purchaserUuid']) ? $this->info['purchaserUuid'] : null)
        ]);
        $this->api->setCacheTime($oldTime);
        if ($player != null) {
            return $player;
        }
        return null;
    }

    public function getOwnerUUID() {
        return isset($this->info['purchaserUuid']) ? $this->info['purchaserUuid'] : null;
    }

    /**
     * @return int
     */
    public function getGameTypeID() {
        return $this->info['gameType'];
    }

    /**
     * @return GameType|null
     */
    public function getGameType() {
        return GameTypes::fromID($this->info['gameType']);
    }

    /**
     * @return bool
     */
    public function isActive() {
        return $this->getLength() != $this->getLength(true);
    }

    /**
     * @param bool $original
     * @return int
     */
    public function getLength($original = false) {
        if ($original) {
            if (isset($this->info['originalLength'])) {
                return $this->info['originalLength'];
            }
            return 3600;
        }
        return $this->info['length'];
    }

    /**
     * @return int
     */
    public function getActivateTime() {
        return $this->info['dateActivated'];
    }

    /**
     * @return array
     */
    public function getInfo() {
        return $this->info;
    }
}

/**
 * Class Leaderboards
 *
 * @package HypixelPHP
 */
class Leaderboards extends HypixelObject {
}

/**
 * Class TimeUtils
 *
 * @package HypixelPHP
 */
class TimeUtils {
    /**
     * @return string
     */
    public static function getWeeklyOscillation() {
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
    public static function getMonthlyOscillation() {
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
    public static function getJavaMonth(DateTime $date) {
        date_default_timezone_set("America/New_York");
        return $date->format("n") - 1;
    }
}