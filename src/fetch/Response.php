<?php

namespace Plancke\HypixelPHP\fetch;

/**
 * Class Response
 * @package Plancke\HypixelPHP\fetch
 */
class Response {

    protected $success = false;
    /** @deprecated */
    protected $errors = [];
    protected $data;

    function __construct() {
    }

    /**
     * @return mixed
     */
    public function wasSuccessful() {
        return $this->success;
    }

    /**
     * @param boolean $success
     * @return $this
     */
    public function setSuccessful($success) {
        $this->success = $success;
        return $this;
    }

    /**
     * @return array
     * @deprecated Actually throwing exceptions now, not adding them to the response
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @param array|string $errors
     * @return $this
     * @deprecated Actually throwing exceptions now, not adding them to the response
     */
    public function addError($errors) {
        if (!is_array($errors)) {
            array_push($this->errors, $errors);
        } else {
            foreach ($errors as $error) {
                array_push($this->errors, $error);
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setData($data) {
        $this->data = $data;
        return $this;
    }

}