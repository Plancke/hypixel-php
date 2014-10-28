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
                'api_key'                => '',
                'base_cache_file_guild'  => 'guild.{ID}.json',
                'base_cache_file_player' => 'player.{NAME}.json'
            ),
            $input
        );
    }

    public function getKey()
    {
        return $this->options['api_key'];
    }

    public function getPlayer($keypair = array())
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
            return new Player($response);

        throw new Exception('Failed:' . $response['cause']);
    }

    public function getGuild($keypair = array())
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
            $filename = str_replace('{ID}', $response['guild'], $this->options['base_cache_file_guild']);
            if(file_exists($filename))
            {
                if(filemtime($filename) < date() - 60)
                {
                    $response = json_decode(file_get_contents('https://api.hypixel.net/guild?key=' . $this->getKey() . '&id=' . $response['guild']), true);
                    if ($response['success'])
                    {
                        $guild = new Guild($response);
                        $file = fopen($filename, 'w');
                        fwrite($file, $guild->getraw());
                        fclose($file);
                    }
                    else
                    {
                        throw new Exception('Failed:' . $response['cause']);
                    }
                }

                $file = fopen($filename, 'w');
                $content = fread($file, filesize($filename));
                fclose($file);
                return $content;
            }


        }
        return null;
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

    public function getPlayerGuild($HypixelAPI)
    {
        return $HypixelAPI->getGuild(array('byPlayer'=>getName()));
    }
}

class Guild extends Object
{
    public function __construct($json)
    {
        $this->infojson = $json['guild'];
    }

    public function getName()
    {
        return get('name', true);
    }

    public function getTag()
    {
        return get('tag', true);
    }

    public function getCreated()
    {
        return get('created');
    }
}