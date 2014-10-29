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
                'api_key'                  => '',
                'cache_file_player_ids'    => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/ids/playerids.json',
                'base_cache_folder_player' => $_SERVER['DOCUMENT_ROOT'] . '/cache/HypixelAPI/player/'
            ),
            $input
        );

        if(!file_exists($this->options['cache_file_player_ids']))
        {
            $file = fopen($this->options['cache_file_player_ids'], 'w');
            fwrite($file, json_encode(array()));
            fclose($file);
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
        $response = file_get_contents('https://api.hypixel.net/' . $request . '?key=' . $this->getKey() . '&' . $key . '=' . $val);
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
            if ($val != '') {
                if($key == 'uuid')
                {
                    $file = fopen($this->options['cache_file_player_ids'], 'w');
                    $content = fread($file, filesize($this->options['cache_file_player_ids']));
                    $content = json_decode($content, true);
                    if(array_key_exists($val, $content))
                    {
                        if(time() - 600 > $content[$val]['timestamp'])
                        {
                            // overwrite old entry cache time exceeded
                            $content[$val] = array(
                                'name'=>'',
                                'timestamp'=>time()
                            );
                        }
                    }
                    else
                    {
                        // new entry
                        $response = $this->fetch('player', $key, $val);
                        $player = new Player($response);
                        $content[$val] = array(
                            'name' => $player->getName(),
                            'timestamp' => time()
                        );
                        return $player;
                    }
                }
                $response = $this->fetch('player', $key, $val);
                break;
            }
        }


    }

}

class Object
{
    public  $infojson;

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

    public function getId()
    {
        return get('id', true);
    }
}

class Player extends Object
{
    public function __construct($json)
    {
        $this->infojson = $json['player'];
    }

    public function getName()
    {
        return get('displayname', true);
    }

    public function getFirstJoin()
    {
        return get('created');
    }
}