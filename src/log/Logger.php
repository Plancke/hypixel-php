<?php

namespace Plancke\HypixelPHP\log;

use Plancke\HypixelPHP\classes\Module;

/**
 * Class Logger
 * @package Plancke\HypixelPHP\log
 */
abstract class Logger extends Module {

    protected $enabled = true;
    protected $formatter;

    /**
     * @param int $level
     * @param string $line
     */
    public function log($level, $line) {
        if (!$this->isEnabled()) return;

        if ($this->getFormatter() != null) {
            $line = $this->getFormatter()->formatLine($level, $line);
        }

        $this->actuallyLog($level, $line);
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
     * @return Formatter|null
     */
    public function getFormatter() {
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

    /**
     * @param int $level
     * @param string $line
     */
    protected abstract function actuallyLog($level, $line);

}