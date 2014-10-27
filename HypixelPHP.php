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
                $response = json_decode(file_get_contents('https://api.hypixel.net/player?key=' . $this->getKey() . '&' . $key . '=' . $val), true);
                break;
            }
        }

        if ($response['success'])
            return new Player($this->getKey(), $response);

        return "";
    }

    public function get_guild($keypair = array())
    {
        $pairs = array_merge(
            array(
                'byPlayer' => '',
                'byName' => '',
                'id' => ''
            ),
            $keypair
        );

        $response = array('success' => 'false');
        foreach ($pairs as $key => $val) {
            if ($val != '') {
                if($key == 'id')
                {
                    $response = array('success'=>'true', 'guild'=>$val);
                    break;
                }

                $response = json_decode(file_get_contents('https://api.hypixel.net/findGuild?key=' . $this->getKey() . '&' . $key . '=' . $val), true);
                break;
            }
        }

        if ($response['success']) {
            $response = json_decode(file_get_contents('https://api.hypixel.net/guild?key=' . $this->getKey() . '&id=' . $response['guild']), true);
            if ($response['success'])
                return new Guild($this->getKey(), $response);
        }
        return null;
    }

}

class Object
{
    public  $infojson;
    public $api_key;

    public function getKey()
    {
        return $this->api_key;
    }

    public function getRaw()
    {
        return $this->infojson;
    }

    public function get($key, $implicit = false)
    {
        if(!$implicit)
        {
            $return = $this->infojson;
            foreach(explode(".", $key) as $split)
            {
                $return = $return[$split];
            }
            return $return;
        }
        return $this->infojson[$key];
    }
}

class Player extends Object
{
    public function __construct($api_key, $json)
    {
        $this->infojson = $json['player'];
        $this->api_key = $api_key;
    }
}

class Guild extends Object
{
    public function __construct($api_key, $json)
    {
        $this->infojson = $json['guild'];
        $this->api_key = $api_key;
    }
}