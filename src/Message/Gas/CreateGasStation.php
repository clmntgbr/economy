<?php

namespace App\Message\Gas;

class CreateGasStation
{
    /** @var string */
    private $stationId;

    /** @var string */
    private $pop;

    /** @var string */
    private $cp;

    /** @var string */
    private $longitude;

    /** @var string */
    private $latitude;

    /** @var string */
    private $street;

    /** @var string */
    private $city;

    /** @var string */
    private $country;

    /** @var array */
    private $element;

    public function __construct(string $stationId, string $pop, string $cp, string $longitude, string $latitude, string $street, string $city, string $country, array $element) {
        $this->stationId = $stationId;
        $this->pop = $pop;
        $this->cp = $cp;
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->street = $street;
        $this->city = $city;
        $this->country = $country;
        $this->element = $element;
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
    public function getPop(): string
    {
        return $this->pop;
    }

    /**
     * @return string
     */
    public function getCp(): string
    {
        return $this->cp;
    }

    /**
     * @return string
     */
    public function getLongitude(): string
    {
        return $this->longitude;
    }

    /**
     * @return string
     */
    public function getLatitude(): string
    {
        return $this->latitude;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return array
     */
    public function getElement(): array
    {
        return $this->element;
    }
}