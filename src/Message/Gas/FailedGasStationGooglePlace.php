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

    /** @var array */
    private $nearBy;

    public function __construct(string $stationId, bool $isGoogled, bool $isForced, array $nearBy) {
        $this->stationId = $stationId;
        $this->isGoogled = $isGoogled;
        $this->isForced = $isForced;
        $this->nearBy = $nearBy;
    }

    /**
     * @return array
     */
    public function getNearBy(): array
    {
        return $this->nearBy;
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