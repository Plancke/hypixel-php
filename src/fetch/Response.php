<?php

namespace Plancke\HypixelPHP\fetch;

class Response {

    protected $success = false;
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
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @param array $errors
     * @return $this
     */
    public function addError($errors) {
        foreach ($errors as $error) {
            array_push($this->errors, $error);
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

    function __toString() {
        return "Response{success=$this->success, data=$this->data, errors=$this->errors}";
    }

}