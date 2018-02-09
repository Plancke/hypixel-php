<?php

namespace Plancke\HypixelPHP\log\impl;

use Plancke\HypixelPHP\HypixelPHP;
use Plancke\HypixelPHP\log\Logger;

/**
 * Class DefaultLogger
 * @package Plancke\HypixelPHP\log\impl
 */
class DefaultLogger extends Logger {

    /**
     * Size of the individual files, in bytes (100MB by default)
     */
    protected $size = 100 * 1024 * 1024;

    /**
     * DefaultLogger constructor.
     * @param HypixelPHP $HypixelPHP
     */
    function __construct(HypixelPHP $HypixelPHP) {
        parent::__construct($HypixelPHP);

        $this->setFormatter(new DefaultFormatter());
    }

    /**
     * @return int
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function setSize($size) {
        $this->size = $size;
        return $this;
    }

    /**
     * Log $string to log files
     * Directory setup:
     *  - LOG_FOLDER/DATE/0.log
     *  - LOG_FOLDER/DATE/1.log
     * separated every {@link $this->size}
     * @param string $line
     */
    public function actuallyLog($line) {
        $dirName = $this->log_folder . DIRECTORY_SEPARATOR . date("Y-m-d");
        if (!file_exists($dirName)) {
            mkdir($dirName, 0777, true);
        }

        $scanDir = array_diff(scandir($dirName), ['.', '..']);
        $numberOfLogs = max(sizeof($scanDir) - 1, 0);

        $filename = $dirName . DIRECTORY_SEPARATOR . $numberOfLogs . '.log';
        if (file_exists($filename)) {
            if (filesize($filename) > $this->size) {
                // file is bigger than supplied size
                // make a new file
                $filename = $dirName . DIRECTORY_SEPARATOR . (++$numberOfLogs) . '.log';
            }
        }
        // save the log, with newline
        file_put_contents($filename, $line . "\r\n", FILE_APPEND);
    }
}