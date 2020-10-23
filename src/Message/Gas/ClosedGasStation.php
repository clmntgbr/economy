<?php

namespace App\Message\Gas;

class ClosedGasStation
{
    /** @var string */
    private $stationId;

    /** @var string */
    private $date;

    public function __construct(string $stationId, string $date) {
        $this->stationId = $stationId;
        $this->date = $date;
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
    public function getDate(): string
    {
        return $this->date;
    }
}