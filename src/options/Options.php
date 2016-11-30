<?php

namespace Plancke\HypixelPHP\options;

use Plancke\HypixelPHP\classes\Module;

class Options extends Module {

    private $options = [];

    /**
     * Manually set option array
     *
     * @param $options
     * @return $this
     */
    public function _setOptions($options) {
        $this->options = $options;
        return $this;
    }

    /**
     * @param $input
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