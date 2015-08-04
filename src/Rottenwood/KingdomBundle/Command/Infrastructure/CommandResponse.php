<?php

namespace Rottenwood\KingdomBundle\Command\Infrastructure;

class CommandResponse {

    private $data;
    private $mapData;
    private $errors = [];
    private $command;

    /**
     * @param string $command
     * @param array  $data
     * @param array  $mapData
     */
    public function __construct($command, array $data = [], array $mapData = []) {
        $this->data = $data;
        $this->mapData = $mapData;
        $this->command = $command;
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
    public function getData() {
        return array_filter([
            'command' => $this->command,
            'data'    => $this->data,
            'mapData' => $this->mapData,
            'errors'  => $this->errors,
        ]);
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
