<?php

namespace App\Message\Gas;

use DateTime;

class ClosedGasStation
{
    /** @var string */
    private $stationId;

    /** @var DateTime */
    private $date;

    public function __construct(string $stationId, DateTime $date) {
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
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }
}