<?php

namespace Rottenwood\KingdomBundle\Command\Infrastructure;

class CommandResponse {

    private $result;
    private $mapData;
    private $errors = [];

    /**
     * @param array $result
     * @param array $mapData
     */
    public function __construct(array $result = [], array $mapData = []) {
        $this->result = $result;
        $this->mapData = $mapData;
    }

    public function addError($error) {
        $this->errors[] = $error;
    }

    public function result() {
        $result = [];

        $result['mapData'] = $this->mapData ?: null;
        $result['result'] = $this->result ?: null;
        $result['errors'] = $this->errors ?: null;

        return array_filter($result);
    }

    /**
     * @return array
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * @param array $result
     */
    public function setResult($result) {
        $this->result = $result;
    }

    /**
     * @return array
     */
    public function getMapData() {
        return $this->mapData;
    }

    /**
     * @param array $mapData
     */
    public function setMapData($mapData) {
        $this->mapData = $mapData;
    }
}
