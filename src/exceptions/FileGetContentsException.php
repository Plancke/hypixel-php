<?php

namespace Plancke\HypixelPHP\exceptions;

/**
 * Class FileGetContentsException
 * @package Plancke\HypixelPHP\exceptions
 */
class FileGetContentsException extends HypixelPHPException {

    protected $fileName;

    /**
     * FileGetContentsException constructor.
     * @param string $fileName
     */
    public function __construct($fileName) {
        parent::__construct("Failed to get file contents.", ExceptionCodes::FILE_GET_CONTENTS);

        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getFileName() {
        return $this->fileName;
    }

}