<?php

namespace App\Message\Gas;

class CreateGasService
{
    /** @var string */
    private $stationId;

    /** @var string */
    private $name;

    public function __construct(string $stationId, string $name) {
        $this->stationId = $stationId;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getStationId(): string
    {
        return $this->stationId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}