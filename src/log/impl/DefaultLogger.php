<?php

namespace Plancke\HypixelPHP\log\impl;

use Plancke\HypixelPHP\log\Logger;

class DefaultLogger extends Logger {

    /**
     * Size of the individual files, in bytes
     */
    protected $size = 512000000;

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
     * @param $line
     */
    public function log($line) {
        if (!$this->isEnabled()) {
            return;
        }

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
        // save the log
        file_put_contents($filename, $this->formatLine($line), FILE_APPEND);
    }

    private function formatLine($line) {
        return '[' . date("H:i:s") . '] ' . $line . "\r\n";
    }
}