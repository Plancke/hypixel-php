<?php
<<<<<<< HEAD
    namespace HypixelPHP;

    /**
     * HypixelPHP
     * @author  Plancke
     * @version 1.4.0
     * @link    http://plancke.nl
     */
    class HypixelPHP
    {
        private $options;
        const MAX_CACHE_TIME = 999999999999;

        /**
         * @param array $input
         */
        public function  __construct($input = [])
        {
            $this->options = array_merge(
                [
                    'api_key'               => '', // Your Hypixel API-key
                    'cache_time'            => 600, // Time to cache statistics, in seconds
                    'cache_folder_player'   => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/player', // Cache folder for playerdata
                    'cache_folder_guild'    => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/guild', // Cache folder for guild data
                    'cache_folder_friends'  => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/friends', // Cache folder for friend data
                    'cache_folder_sessions' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/sessions', // Cache folder for session data
                    'cache_boosters'        => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/boosters.json', // Cache file for booster data
                    'cache_leaderboards'    => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/leaderboards.json', // Cache file for leaderboards
                    'cache_keyInfo'         => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/keyInfo.json',
                    'debug'                 => false, // Enable or disable debug messages
                    'log'                   => false, // Enable or disable logging to file
                    'timeout'               => 2, // Timeout to wait for connecting and waiting on the API server and other sites, in seconds. Longer may be more stable, but also more annoying to end-users.
                    'use_curl'              => true
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
        }
=======
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
                'timeout' => 2,
                'cache_folder_player' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/player',
                'cache_folder_guild' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/guild',
                'cache_folder_friends' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/friends',
                'cache_folder_sessions' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/sessions',
                'cache_boosters' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/boosters.json',
                'cache_leaderboards' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/leaderboards.json',
                'cache_keyInfo' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/keyInfo.json',
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
        if (!file_exists($this->options['log_folder'])) {
            mkdir($this->options['log_folder'], 0777, true);
        }
    }
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2

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
        }
<<<<<<< HEAD
=======
        if ($this->options['logging']) {
            $this->log($message);
        }
    }
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2

        /**
         * @param $key
         */
        public function setKey($key)
        {
            $this->set(['api_key' => $key]);
        }

        /**
         * @return mixed
         */
        public function getKey()
        {
            return $this->options['api_key'];
        }

<<<<<<< HEAD
        /**
         * @return KeyInfo|null
         */
        public function getKeyInfo()
        {
            $filename = $this->options['cache_keyInfo'];
            $content  = $this->getCache($filename);
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
                $content = ['timestamp' => time(), 'record' => $response['record']];
                $this->setFileContent($filename, json_encode($content));
                return new KeyInfo($content, $this);
            }
            return null;
        }

        /**
         * @return array
         */
        public function getOptions()
        {
            return $this->options;
        }

        /**
         * @param        $name
         * @param string $url
         *
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
         *
         * @return array|mixed
         */
        public function getUrlContents($url)
        {
            $timeout  = $this->options['timeout'];
            $errorOut = ["success" => false, 'cause' => 'Timeout'];
            if ($this->options['use_curl']) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
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
=======
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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
            }
        }

<<<<<<< HEAD
        public function getCacheTime()
        {
            return $this->options['cache_time'];
        }

        /**
         * @param int $cache_time
         */
        public function setCacheTime($cache_time = 600)
        {
            $this->set(['cache_time' => $cache_time]);
=======
    /**
     * Returns the currently set cache threshold
     * @return int
     */
    public function getCacheTime()
    {
        return $this->options['cache_time'];
    }

    public function setCacheTime($cache_time = 600)
    {
        $this->set(array('cache_time' => $cache_time));
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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
        }
        $this->debug('Starting Fetch: ' . $debug);

<<<<<<< HEAD
        /**
         * @param $string
         */
        public function log($string)
        {
            if ($this->options['log']) {
                $dirName = $this->options['logs'] . DIRECTORY_SEPARATOR . date("Y-m-d");
                if (!file_exists($dirName)) {
                    mkdir($dirName, 0777, true);
                }
                $scanDir      = array_diff(scandir($dirName), ['.', '..']);
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
=======
        $response = $this->getUrlContents($requestURL, $timeout);
        if ($response['success'] == false) {
            if (!array_key_exists('cause', $response)) {
                $response['cause'] = 'Unknown';
            }
            $this->debug('Fetch Failed: ' . $response['cause']);
        } else {
            $this->debug('Fetch successful!');
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
        }

<<<<<<< HEAD
        /**
         * @param      $request
         * @param null $key
         * @param null $val
         *
         * @return array|mixed
         */
        public function fetch($request, $key = null, $val = null)
        {
            if ($this->getCacheTime() >= self::MAX_CACHE_TIME) {
                $return = ["success" => false, 'cause' => 'Max Cache Time!'];
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

        /**
         * @param array $keyPair
         *
         * @return Player|null
         */
        public function getPlayer($keyPair = [])
        {
            $pairs = array_merge(
                [
                    'name' => '',
                    'uuid' => ''
                ],
                $keyPair
            );

            foreach ($pairs as $key => $val) {
                if ($val != null) {
                    $filename = $this->options['cache_folder_player'] . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $this->getCacheFileName($val) . '.json';
                    if ($key == 'uuid') {
=======
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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
                        $content = $this->getCache($filename);
                        if ($content != null) {
                            $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                            if (time() - $this->getCacheTime() < $timestamp) {
<<<<<<< HEAD
                                $this->debug('Getting Cached data!');
                                return $this->getPlayer(['name' => $content['name']]);
                            }
                        }

                        $response = $this->fetch('player', $key, $val);
                        if ($response['success'] == 'true') {
                            $content = ['timestamp' => time(), 'name' => $response['player']['displayname']];
                            $this->setFileContent($filename, json_encode($content));
                            return $this->getPlayer(['name' => $content['name']]);
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
                                    $PLAYER = new Player([
                                        'record' => $response['player'],
                                        'extra'  => $content['extra']
                                    ], $this);
                                    $PLAYER->setExtra(['filename' => $filename]);
                                    $this->setCache($filename, $PLAYER);
                                    return $PLAYER;
                                }
                            }
=======
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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
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

<<<<<<< HEAD
        /**
         * @param array $keyPair
         *
         * @return Guild|null
         */
        public function getGuild($keyPair = [])
        {
            $pairs = array_merge(
                [
                    'byPlayer' => null,
                    'byName'   => null,
                    'id'       => null
                ],
                $keyPair
            );

            foreach ($pairs as $key => $val) {
                if ($val != null) {
                    if ($key == 'byPlayer' && $val != null) {
                        if (!$this->hasPaid($val)) {
=======
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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
                            continue;
                        }
                    }
                    $filename = $this->options['cache_folder_guild'] . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $this->getCacheFileName($val) . '.json';

<<<<<<< HEAD
                    if ($key == 'byPlayer' || $key == 'byName') {
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
=======
                    $response = $this->fetch('findGuild', $key, $val, 5);
                    if ($response['success'] == 'true') {
                        $content = array('timestamp' => time(), 'guild' => $response['guild']);
                        $this->setFileContent($filename, json_encode($content));
                        return $this->getGuild(array('id' => $response['guild']));
                    }
                }
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2

                        $response = $this->fetch('findGuild', $key, $val);
                        if ($response['success'] == 'true') {
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
                        if ($response['success'] == 'true') {
                            $GUILD = new Guild([
                                'record' => $response['guild'],
                                'extra'  => $content['extra']
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

<<<<<<< HEAD
        /**
         * @param array $keyPair
         *
         * @return Session|null
         */
        public function getSession($keyPair = [])
        {
            $pairs = array_merge(
                [
                    'player' => null
                ],
                $keyPair
            );

            foreach ($pairs as $key => $val) {
                if ($val != null) {
                    if ($key == 'player') {

                        $filename = $this->options['cache_folder_sessions'] . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $this->getCacheFileName($val) . '.json';
                        $content  = $this->getCache($filename);
                        if ($content != null) {
                            $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                            if (time() - $this->getCacheTime() < $timestamp) {
                                return new Session($content, $this);
                            }
=======
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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
                        }

<<<<<<< HEAD
                        $response = $this->fetch('session', $key, $val);
                        if ($response['success'] == 'true') {
                            $SESSION = new Session([
                                'record' => $response['session'],
                                'extra'  => $content['extra']
                            ], $this);
                            $SESSION->setExtra(['filename' => $filename]);
                            $this->setCache($filename, $SESSION);
                            return $SESSION;
                        }
=======
                    $response = $this->fetch('session', $key, $val->getName());
                    if ($response['success'] == 'true') {
                        $SESSION = new Session(array(
                            'record' => $response['session'],
                            'extra' => $content['extra']
                        ), $this);
                        $SESSION->setExtra(array('filename' => $filename));
                        $this->setCache($filename, $SESSION);
                        return $SESSION;
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
                    }
                }
            }

            if ($this->getCacheTime() < self::MAX_CACHE_TIME) {
                $this->setCacheTime(self::MAX_CACHE_TIME);
                return $this->getSession($pairs);
            }
            return null;
        }

<<<<<<< HEAD
        /**
         * @param array $keyPair
         *
         * @return Friends|null
         */
        public function getFriends($keyPair = [])
        {
            $pairs = array_merge(
                [
                    'player' => null
                ],
                $keyPair
            );

            foreach ($pairs as $key => $val) {
                if ($val != null) {
                    if ($key == 'player') {
                        $filename = $this->options['cache_folder_friends'] . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $this->getCacheFileName($val) . '.json';
                        $content  = $this->getCache($filename);
                        if ($content != null) {
                            $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                            if (time() - $this->getCacheTime() < $timestamp) {
                                return new Friends($content, $this);
                            }
=======
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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
                        }

<<<<<<< HEAD
                        $response = $this->fetch('friends', $key, $val);
                        if ($response['success'] == 'true') {
                            $FRIENDS = new Friends([
                                'record' => $response['records'],
                                'extra'  => $content['extra']
                            ], $this);
                            $FRIENDS->setExtra(['filename' => $filename]);
                            $this->setCache($filename, $FRIENDS);
                            return $FRIENDS;
                        }
=======
                    $response = $this->fetch('friends', $key, $val->getName());
                    if ($response['success'] == 'true') {
                        $FRIENDS = new Friends(array(
                            'record' => $response['records'],
                            'extra' => $content['extra']
                        ), $this);
                        $FRIENDS->setExtra(array('filename' => $filename));
                        $this->setCache($filename, $FRIENDS);
                        return $FRIENDS;
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
                    }
                }
            }

            if ($this->getCacheTime() < self::MAX_CACHE_TIME) {
                $this->setCacheTime(self::MAX_CACHE_TIME);
                return $this->getFriends($pairs);
            }
            return null;
        }

<<<<<<< HEAD
        /**
         * @return Boosters|null
         */
        public function getBoosters()
        {
            $filename = $this->options['cache_boosters'];
            $content  = $this->getCache($filename);
            if ($content != null) {
                $timestamp = array_key_exists('timestamp', $content) ? $content['timestamp'] : 0;
                if (time() - $this->getCacheTime() < $timestamp) {
                    return new Boosters($content, $this);
                }
=======
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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
            }

            $response = $this->fetch('boosters');
            if ($response['success'] == 'true') {
                $BOOSTERS = new Boosters([
                    'record' => $response['boosters'],
                    'extra'  => $content['extra']
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

<<<<<<< HEAD
        /**
         * @return Leaderboards|null
         */
        public function getLeaderboards()
        {
            $filename = $this->options['cache_leaderboards'];
            $content  = $this->getCache($filename);
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
                    'extra'  => $content['extra']
                ], $this);
                $LEADERBOARDS->setExtra(['filename' => $filename]);
                $this->setCache($filename, $LEADERBOARDS);
                return $LEADERBOARDS;
=======
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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
            }

            if ($this->getCacheTime() < self::MAX_CACHE_TIME) {
                $this->setCacheTime(self::MAX_CACHE_TIME);
                return $this->getLeaderboards();
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

<<<<<<< HEAD
        /**
         * @param $filename
         * @param $content
         */
        public function setFileContent($filename, $content)
        {
            $this->debug('Setting contents of ' . $filename);
=======
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

    public function getFileContent($filename)
    {
        $content = null;
        if (file_exists($filename)) {
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
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

<<<<<<< HEAD
        /**
         * @return array
         */
        public function getRanks()
        {
            $ranks = [
                'ADMIN'     => [
                    'prefix' => 'ADMIN',
                    'colors' => [
                        'front' => 'FF5555',
                        'back'  => '3F1515'
                    ]
                ],
                'JR DEV'    => [
                    'prefix' => 'JR DEV',
                    'colors' => [
                        'front' => '55FF55',
                        'back'  => '153F15'
                    ]
                ],
                'MODERATOR' => [
                    'prefix' => 'MOD',
                    'colors' => [
                        'front' => '00AA00',
                        'back'  => '002A00'
                    ]
                ],
                'HELPER'    => [
                    'prefix' => 'HELPER',
                    'colors' => [
                        'front' => '0000AA',
                        'back'  => '00002A'
                    ]
                ],
                'JR HELPER' => [
                    'prefix' => 'JR HELPER',
                    'colors' => [
                        'front' => '0000AA',
                        'back'  => '00002A'
                    ]
                ],
                'YOUTUBER'  => [
                    'prefix' => 'YT',
                    'colors' => [
                        'front' => 'FFAA00',
                        'back'  => '2A2A00'
                    ]
                ],
                'MVP+'      => [
                    'prefix' => 'MVP+',
                    'colors' => [
                        'front' => '22CCCC',
                        'back'  => '153F3F',
                        'plus'  => 'FF5555'
                    ]
                ],
                'MVP'       => [
                    'prefix' => 'MVP',
                    'colors' => [
                        'front' => '22CCCC',
                        'back'  => '153F3F'
                    ]
                ],
                'VIP+'      => [
                    'prefix' => 'VIP+',
                    'colors' => [
                        'front' => '22CC22',
                        'back'  => '153F15',
                        'plus'  => 'FFAA00'
                    ]
                ],
                'VIP'       => [
                    'prefix' => 'VIP',
                    'colors' => [
                        'front' => '22CC22',
                        'back'  => '153F15'
                    ]
                ],
                'DEFAULT'   => [
                    'colors' => [
                        'front' => 'AAAAAA',
                        'back'  => 'A2A2A2'
                    ]
                ],
                'NONE'      => [
                    'prefix' => 'NONE',
                    'colors' => [
                        'front' => 'AAAAAA',
                        'back'  => 'A2A2A2'
                    ]
                ]
            ];

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
=======
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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
    }

    /**
     * Class HypixelObject
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
            $this->api       = $api;
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

<<<<<<< HEAD
        /** @deprecated */
        public function isNull()
        {
            return !array_key_exists('record', $this->JSONArray);
        }

        /**
         * @return array
         */
        public function getRaw()
        {
            return $this->JSONArray;
        }
=======
    public function getRaw()
    {
        return $this->JSONArray;
    }

    public function getRecord()
    {
        return $this->JSONArray['record'];
    }
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2

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

<<<<<<< HEAD
        /**
         * @return bool
         */
        public function isCached()
        {
            return $this->getCacheTime() > 0;
        }

        public function getCacheTime()
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
                if ($val == null) {
                    unset($this->JSONArray['extra'][$key]);
=======
    public function isCached()
    {
        return $this->getCachedTime() > 0;
    }

    public function isCacheExpired()
    {
        return time() - $this->api->getCacheTime() > $this->getCachedTime();
    }

    public function getCachedTime()
    {
        return $this->JSONArray['timestamp'];
    }

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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
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
                $anyChange                      = true;
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

<<<<<<< HEAD
    /**
     * Class KeyInfo
     * @package HypixelPHP
     */
    class KeyInfo extends HypixelObject
=======
}

class Player extends HypixelObject
{
    /**
     * get Session of Player
     * @return Session|null
     */
    public function getSession()
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
    {

<<<<<<< HEAD
    }

    /**
     * Class Player
     * @package HypixelPHP
     */
    class Player extends HypixelObject
    {
        /**
         * @return Session|null
         */
        public function getSession()
        {
            return $this->api->getSession(['player' => $this->getName()]);
        }

        /**
         * @return Friends|null
         */
        public function getFriends()
        {
            return $this->api->getFriends(['player' => $this->getName()]);
        }

        /**
         * @return array|null|string
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
                return $aliases[0];
            }
=======
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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
        }

<<<<<<< HEAD
        /**
         * @param array $rankOptions
         *
         * @return mixed|string
         */
        public function getFormattedPrefix($rankOptions = [false, false])
        {
            $playerRank = $this->getRank($rankOptions[0], $rankOptions[1]);
            $rankInfo   = $this->api->getRankInfo($playerRank);
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

        /**
         * @param array  $rankOptions
         * @param string $extraCSS
         *
         * @return string
         */
        public function getPrefixedName($rankOptions = [false, false], $extraCSS = '')
        {
            $playerRank = $this->getRank($rankOptions[0], $rankOptions[1]);
            $rankInfo   = $this->api->getRankInfo($playerRank);
            return '<span style="color: #' . $rankInfo['colors']['front'] . ';' . $extraCSS . '">' . $this->getFormattedPrefix($rankOptions) . $this->getName() . '</span>';
        }

        /**
         * @param array  $rankOptions
         * @param string $extraCSS
         *
         * @return string
         */
        public function getColoredName($rankOptions = [false, false], $extraCSS = '')
        {
            $playerRank = $this->getRank($rankOptions[0], $rankOptions[1]);
            $rankInfo   = $this->api->getRankInfo($playerRank);
            return '<span style="color: #' . $rankInfo['colors']['front'] . ';' . $extraCSS . '">' . $this->getName() . '</span>';
        }
=======
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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2

        /**
         * @return array|null|string
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
            return new Stats($this->get('stats', true, []), $this->api);
        }

        /**
         * @return array|null|string
         */
        public function isPreEULA()
        {
            return $this->get('eulaCoins', true, false);
        }

        /**
         * @return array|null|string
         */
        public function getLevel()
        {
            return $this->get('networkLevel', true, 0) + 1;
        }

<<<<<<< HEAD
        /**
         * @return array|null|string
         */
        public function getPrefix()
        {
            return $this->get('prefix', true);
        }
=======
    public function getPrefix()
    {
        return $this->get('prefix', false, null);
    }
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2

        /**
         * @return bool
         */
        public function isStaff()
        {
            $rank = $this->get('rank', true, 'NORMAL');
            if ($rank == 'NORMAL') {
                return false;
            }
            return true;
        }

<<<<<<< HEAD
        /**
         * @return float|int
         */
        public function getMultiplier()
        {
            if ($this->getRank(false) == 'YOUTUBER') return 7;
            $ranks    = ['DEFAULT', 'VIP', 'VIP+', 'MVP', 'MVP+'];
            $pre      = $this->getRank(true, true);
            $flip     = array_flip($ranks);
            $rankKey  = $flip[$pre] + 1;
            $levelKey = floor($this->getLevel() / 25) + 1;
            return ($rankKey > $levelKey) ? $rankKey : $levelKey;
        }

        /**
         * @param bool $package
         * @param bool $preEULA
         *
         * @return mixed
         */
        public function getRank($package = true, $preEULA = false)
        {
            $return = 'DEFAULT';
            if ($package) {
                $keys = ['newPackageRank', 'packageRank'];
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
=======
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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
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
<<<<<<< HEAD
            if ($return == 'NONE' && $preEULA) {
                return $this->getRank($package, !$preEULA);
            }
            return str_replace('_', ' ', str_replace('_PLUS', '+', $return));
=======
        } else {
            if (!$this->isStaff()) return $this->getRank(true, $preEULA);
            $return = $this->get('rank', true);
        }
        if ($return == 'NONE' && $preEULA) {
            return $this->getRank($package, !$preEULA);
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
        }

<<<<<<< HEAD
        /**
         * @return string
         */
        public function getAchievementPoints()
        {
            return 'WIP';
        }

        /**
         * @param      $key
         * @param bool $implicit
         * @param null $default
         *
         * @return array|null|string
         */
        public function get($key, $implicit = false, $default = null)
        {
            if ($key == 'achievementPoints') {
                return $this->getAchievementPoints();
            }
            return parent::get($key, $implicit, $default);
        }
    }

    /**
     * Class Achievements
     * @package HypixelPHP
     */
    class Achievements extends HypixelObject
    {
        const GAME_GENERAL   = "general";
        const GAME_QUAKE     = "quake";
        const GAME_PAINTBALL = "paintball";
        const GAME_WALLS     = "walls";
        const GAME_VAMPIREZ  = "vampirez";
        const GAME_BLITZ     = "blitz";
        const GAME_MEGAWALLS = "walls3";
        const GAME_ARENA     = "arena";

        const GAME_ARCADE               = "arcade";
        const GAME_ARCADE_CREEPERATTACK = "arcade.creeper";

        const GAME_TNTGAMES         = "tntgames";
        const GAME_TNTGAMES_WIZARDS = "tntgames.wizards";
        const GAME_TNTGAMES_BOW     = "tntgames.bow";
        const GAME_TNTGAMES_TNT_RUN = "tntgames.tnt.run";
        const GAME_TNTGAMES_TNT_TAG = "tntgames.tnt.tag";


        private $player;

        /**
         * @param            $json
         * @param HypixelPHP $api
         * @param            $player
         */
        public function __construct($json, $api, $player)
        {
            parent::__construct($json, $api);
            $this->player = $player;
        }

        /**
         * @param $key
         *
         * @return array|null
         */
        public function getByGame($key /* should be the game name */)
        {
            $achievements       = $this->JSONArray;
            $availableGamesList = [];
            foreach ($achievements as $achievement) {
                $explode  = explode('_', $achievement);
                $gameName = $explode[0];
                if (!in_array($gameName, array_keys($availableGamesList))) {
                    $availableGamesList[$gameName] = [];
                }
                if (in_array($gameName, ["arcade", "tntgames"])) {
                    $subGameName = $explode[1];
                    if (!in_array($subGameName, array_keys($availableGamesList[$gameName]))) $availableGamesList[$gameName][$subGameName] = [];
                    $availableGamesList[$gameName][$subGameName][] = $achievement;
                } else {
                    $availableGamesList[$gameName][] = $achievement;
                }
            }
            if (count($availableGamesList) <= 0) {
                return null;
            }
            $tmpGameList = $availableGamesList;
            foreach (explode('.', $key) as $subGame) {
                if (in_array($subGame, array_keys($tmpGameList))) {
                    $tmpGameList = $tmpGameList[$subGame];
                } else return null;
            }
            $ret = [];
            foreach ($tmpGameList as $game => $achievement) {
                $ret[] = new OneTimeAchievement($achievement, $this->player);
            }
            return $ret;
        }

        /**
         * @param $ach
         *
         * @return bool
         */
        public function hasAchievement($ach)
        {
            return in_array($ach, $this->JSONArray);
        }
    }

    /**
     * Class OneTimeAchievement
     * @package HypixelPHP
     */
    class OneTimeAchievement
    {

        private $name;
        private $player;

        /**
         * @param null $key
         * @param null $player
         */
        public function __construct($key = null, $player = null)
        {
            $this->name   = $key;
            $this->player = $player;
        }

        /**
         * @return bool
         */
        public function isNull()
        {
            return $this->name == null || !is_string($this);
        }

        /**
         * @return null
         */
        public function getName()
        {
            return $this->name;
        }

        public function getDescription()
        {
            // not done for now
        }
=======
    /**
     * get Player achievement points
     * @return int
     */
    public function getAchievementPoints()
    {
        return 0;
    }

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

    public function getRankInfo($rank = 'NONE')
    {
        $rankInfo = $this->getRanks();
        if (!array_key_exists($rank, $rankInfo)) {
            $rank = 'NONE';
        }
        return $rankInfo[$rank];
    }

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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2

        /**
         * @return null|string
         */
        public function __toString()
        {
            if ($this->isNull()) {
                return "";
            }
            return $this->name;
        }
    }

    /**
     * Class TieredAchievement
     * @package HypixelPHP
     */
    class TieredAchievement
    {
    }

    /**
     * Class Tier
     * @package HypixelPHP
     */
    class Tier
    {
    }

<<<<<<< HEAD
    /**
     * Class Stats
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
=======
    public function getPackages()
    {
        return $this->get('packages', false, array());
    }

    public function getCoins()
    {
        return $this->get('coins', false, 0);
    }
}
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2

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

<<<<<<< HEAD
    /**
     * Class GameStats
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
=======
    public function getGameType()
    {
        return $this->get('gameType', true);
    }
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2

        /**
         * @return array|null
         */
        public function getPackages()
        {
            return $this->get('packages', false, []);
        }
    }

    /**
     * Class Session
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
        public function getGame()
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
            parent::__construct(['record' => $json], $api);
        }
    }

    /**
     * Class Guild
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

<<<<<<< HEAD
        /**
         * @return array|null
         */
        public function getCoins()
        {
            return $this->get('coins', true);
        }
=======
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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2

        /**
         * @return MemberList
         */
        public function getMemberList()
        {
            if ($this->members == null) {
                $this->members = new MemberList($this->get('members'), $this->api);
            }
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
    }

    /**
     * Class MemberList
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

            $list        = ["GUILDMASTER" => [], "OFFICER" => [], "MEMBER" => []];
            $this->count = sizeof($json);
            foreach ($json as $player) {
                $rank = $player['rank'];
                if (!in_array($rank, array_keys($list))) {
                    $list[$rank] = [];
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
     * @package HypixelPHP
     */
    class GameTypes
    {
        const QUAKE     = 2;
        const WALLS     = 3;
        const PAINTBALL = 4;
        const BSG       = 5;
        const TNTGAMES  = 6;
        const VAMPIREZ  = 7;
        const MEGAWALLS = 13;
        const ARCADE    = 14;
        const ARENA     = 17;
        const UHC       = 20;
        const MCGO      = 21;

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
            $this->db    = $db;
            $this->name  = $name;
            $this->short = $short;
            $this->id    = $id;
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

<<<<<<< HEAD
    /**
     * Class Boosters
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
        public function getQueue($gameType = GameTypes::ARCADE, $max = 10)
        {
            $return = [
                'boosters' => [],
                'total'    => 0
            ];
            foreach ($this->JSONArray['record'] as $boosterInfo) {
                $booster = new Booster($boosterInfo, $this->api);
                if ($booster->getGameTypeID() == $gameType) {
                    if ($return['total'] < $max) {
                        array_push($return['boosters'], $booster);
                    }
                    $return['total']++;
=======
class Boosters extends HypixelObject
{
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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
                }
            }
            return $return;
        }

<<<<<<< HEAD
        /**
         * @param $playerName
         *
         * @return array
         */
        public function getBoosters($playerName)
        {
            $boosters = [];
            foreach ($this->JSONArray['record'] as $boosterInfo) {
                $booster = new Booster($boosterInfo, $this->api);
                if (strtolower($booster->getOwner()) == strtolower($playerName)) {
=======
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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2
                    array_push($boosters, $booster);
                }
            }
            return $boosters;
        }
    }

    /**
<<<<<<< HEAD
     * Class Booster
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
            $this->api  = $api;
        }
=======
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
>>>>>>> 10f249cf27c208497cfd9610fb9dea7b31508bf2

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
            $player = $this->api->getPlayer(['uuid' => $this->info['purchaserUuid']]);
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
         *
         * @return int
         */
        public function getLength($original = false)
        {
            if ($original) {
                return $this->info['originalLength'];
            }
            return $this->info['length'];
        }

        public function getActivateTime()
        {
            return $this->info['dateActivated'];
        }
    }

    /**
     * Class Leaderboards
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
            parent::__construct(['record' => $json], $api);
        }
    }
