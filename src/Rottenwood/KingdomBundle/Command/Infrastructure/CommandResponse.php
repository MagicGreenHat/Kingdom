<?php

namespace Rottenwood\KingdomBundle\Command\Infrastructure;

class CommandResponse {

    private $data;
    private $mapData;
    private $errors = [];
    private $commandName;
    private $waitState;

    /**
     * @param string $commandName
     * @param array  $data
     * @param array  $mapData
     */
    public function __construct($commandName, array $data = [], array $mapData = []) {
        $this->data = $data;
        $this->mapData = $mapData;
        $this->commandName = $commandName;
    }

    /**
     * @param string $error
     */
    public function addError(string $error) {
        $this->errors[] = $error;
    }

    /**
     * @return array
     */
    public function getData(): array {
        return array_filter([
            'commandName' => $this->commandName,
            'data'        => $this->data,
            'mapData'     => $this->mapData,
            'waitstate'   => $this->waitState,
            'errors'      => $this->errors,
        ]);
    }

    /**
     * @param array $data
     */
    public function setData(array $data) {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getMapData(): array {
        return $this->mapData;
    }

    /**
     * @param array $mapData
     */
    public function setMapData(array $mapData) {
        $this->mapData = $mapData;
    }

    /**
     * @param int $waitState
     */
    public function setWaitstate(int $waitState) {
        $this->waitState = $waitState;
    }
}
