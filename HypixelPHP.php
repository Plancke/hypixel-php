<?php

/**
 * HypixelPHP
 *
 * @author Plancke @f6design
 * @version 1.0.0
 * @link  http://plancke.nl
 *
 */
class HypixelPHP
{
    private $options;

    public function  __construct($options = array())
    {

        $this->options = array_merge(
            array(
                'api_key' => ''
            ),
            $options
        );
    }

    public function get_player($keypair = array())
    {
        $pairs = array_merge(
            array(
                'name' => '',
                'uuid' => '',
                'id' => ''
            ),
            $keypair
        );

        $response = array('succes' => 'false');
        foreach ($pairs as $key => $val) {
            if ($val != '') {
                $response = json_decode(http_get('https://api.hypixel.net/player?key=' . $this->options['api_key'] . '&' . $key . '=' . $val));
                break;
            }
        }

        if ($response['succes'])
            return new Player($this->options['api_key'], $response);

        return null;
    }


}

class Player
{
    private $infojson;
    private $api_key;
    private $guild;

    public function __construct($api_key, $json)
    {
        $this->infojson = $json;
        $this->api_key = $api_key;
        $this->guild = $this->getGuild();
    }

    public function getAPI()
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

        $id = json_decode(http_get('https://api.hypixel.net/findGuild?key=' . $this->api_key . '&byPlayer' . $this->get('displayname')));
        if ($id['succes']) {
            $guild = json_decode(http_get('https://api.hypixel.net/guild?key=' . $this->api_key . '&id' . $id['_id']));
            if ($guild['success'])
                return new Guild($this->api_key, $guild);
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
        $this->infojson = $json;
        $this->api_key = $api_key;
    }

    public function getAPI()
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