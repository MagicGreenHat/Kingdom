<?php

namespace Rottenwood\KingdomBundle\Command\Infrastructure;

class CommandResponse {

    private $data;
    private $mapData;
    private $errors = [];

    /**
     * @param array $data
     * @param array $mapData
     */
    public function __construct(array $data = [], array $mapData = []) {
        $this->data = $data;
        $this->mapData = $mapData;
    }

    /**
     * @param string $error
     */
    public function addError($error) {
        $this->errors[] = $error;
    }

    /**
     * @return array
     */
    public function result() {
        if ($this->mapData) {
            $this->data['mapData'] = $this->mapData;
        }

        return $this->errors ? ['errors' => $this->errors] : $this->data;
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data) {
        $this->data = $data;
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
