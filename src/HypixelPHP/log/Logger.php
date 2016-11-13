<?php

namespace Plancke\HypixelPHP\log;

use Plancke\HypixelPHP\classes\Module;

abstract class Logger extends Module {

    protected $enabled;
    protected $log_folder;

    /**
     * @return mixed
     */
    public function isEnabled() {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     * @return $this
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogFolder() {
        return $this->log_folder;
    }

    /**
     * @param string $log_folder
     * @return $this
     */
    public function setLogFolder($log_folder) {
        $this->log_folder = $log_folder;
        return $this;
    }

    public abstract function log($line);

}