<?php

namespace App\Message\Gas;

class CreateGasStationGooglePlace
{
    /** @var string */
    private $stationId;

    /** @var string */
    private $placeId;

    /** @var float */
    private $distance;

    /** @var float */
    private $similarText;

    /** @var array */
    private $nearBy;

    public function __construct(string $stationId, string $placeId, float $distance, float $similarText, array $nearBy) {
        $this->stationId = $stationId;
        $this->placeId = $placeId;
        $this->distance = $distance;
        $this->similarText = $similarText;
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
     * @return float
     */
    public function getDistance(): float
    {
        return $this->distance;
    }

    /**
     * @return float
     */
    public function getSimilarText(): float
    {
        return $this->similarText;
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
    public function getPlaceId(): string
    {
        return $this->placeId;
    }
}