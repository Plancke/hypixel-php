<?php

namespace Plancke\HypixelPHP\options;

use Plancke\HypixelPHP\classes\Module;

/**
 * Class Options
 * @package Plancke\HypixelPHP\options
 */
class Options extends Module {

    protected $options = [];

    /**
     * Manually set option array
     *
     * @param array $options
     * @return $this
     */
    public function _setOptions($options) {
        $this->options = $options;
        return $this;
    }

    /**
     * @param array $input
     * @return $this
     */
    public function setOptions($input) {
        foreach ($input as $key => $val) {
            if ($this->options[$key] != $val) {
                if (is_array($val)) {
                    $this->getHypixelPHP()->getLogger()->log('Setting ' . $key . ' to ' . json_encode($val));
                } else {
                    $this->getHypixelPHP()->getLogger()->log('Setting ' . $key . ' to ' . $val);
                }
            }
            $this->options[$key] = $val;
        }
        return $this;
    }

}