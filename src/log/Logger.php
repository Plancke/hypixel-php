<?php

namespace Plancke\HypixelPHP\log;

use Plancke\HypixelPHP\classes\Module;
use Plancke\HypixelPHP\log\impl\DefaultFormatter;

/**
 * Class Logger
 * @package Plancke\HypixelPHP\log
 */
abstract class Logger extends Module {

    protected $enabled = true;
    protected $log_folder;
    protected $formatter;

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

    /**
     * @param string $line
     */
    public function log($line) {
        if (!$this->isEnabled()) {
            return;
        }

        $this->actuallyLog($this->getFormatter()->formatLine($line));
    }

    /**
     * @return boolean
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
     * @param string $line
     */
    protected abstract function actuallyLog($line);

    /**
     * @return Formatter
     */
    public function getFormatter() {
        if ($this->formatter == null) {
            $this->setFormatter(new DefaultFormatter());
        }
        return $this->formatter;
    }

    /**
     * @param Formatter $formatter
     * @return $this
     */
    public function setFormatter($formatter) {
        $this->formatter = $formatter;
        return $this;
    }

}