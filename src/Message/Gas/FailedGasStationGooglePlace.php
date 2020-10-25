<?php

namespace App\Message\Gas;

class FailedGasStationGooglePlace
{
    /** @var string */
    private $stationId;

    /** @var bool */
    private $isGoogled;

    /** @var bool */
    private $isForced;

    public function __construct(string $stationId, bool $isGoogled, bool $isForced) {
        $this->stationId = $stationId;
        $this->isGoogled = $isGoogled;
        $this->isForced = $isForced;
    }

    /**
     * @return string
     */
    public function getStationId(): string
    {
        return $this->stationId;
    }

    /**
     * @return bool
     */
    public function isGoogled(): bool
    {
        return $this->isGoogled;
    }

    /**
     * @return bool
     */
    public function isForced(): bool
    {
        return $this->isForced;
    }
}