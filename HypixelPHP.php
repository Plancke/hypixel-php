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
                'api_key' => ''
            ),
            $input
        );
    }

    public function getKey()
    {
        return $this->options['api_key'];
    }

    public function get_player($keypair = array())
    {
        $pairs = array_merge(
            array(
                'name' => '',
                'uuid' => '',
                'id'   => ''
            ),
            $keypair
        );

        $response = array('success' => 'false');
        foreach ($pairs as $key => $val) {
            if ($val != '') {
                $response = json_decode(file_get_contents('https://api.hypixel.net/player?key=' . $this->options['api_key'] . '&' . $key . '=' . $val), true);
                break;
            }
        }

        if ($response['success'])
            return new Player($this->options['api_key'], $response);

        return "";
    }

}

class Player
{
    private $infojson;
    private $api_key;
    private $guild;

    public function __construct($api_key, $json)
    {
        $this->infojson = $json['player'];
        $this->api_key = $api_key;
        $this->guild = $this->getGuild();
    }

    public function getKey()
    {
        return $this->api_key;
    }

    public function getRaw()
    {
        return $this->infojson;
    }

    public function get($key)
    {
        return $this->infojson[$key];
    }

    public function getGuild()
    {
        if ($this->guild != null)
            return $this->guild;

        echo 'https://api.hypixel.net/findGuild?key=' . $this->api_key . '&byPlayer=' . $this->get('displayname');

        $response = json_decode(file_get_contents('https://api.hypixel.net/findGuild?key=' . $this->getKey() . '&byPlayer=' . $this->get('displayname')), true);
        if ($response['success']) {
            $response = json_decode(file_get_contents('https://api.hypixel.net/guild?key=' . $this->getKey() . '&id=' . $response['guild']), true);
            if ($response['success'])
                return new Guild($this->api_key, $response);
        }
        return null;
    }
}

class Guild
{
    private $infojson;
    private $api_key;

    public function __construct($api_key, $json)
    {
        $this->infojson = $json['guild'];
        $this->api_key = $api_key;
    }

    public function getKey()
    {
        return $this->api_key;
    }

    public function getRaw()
    {
        return $this->infojson;
    }

    public function get($key)
    {
        return $this->infojson[$key];
    }
}