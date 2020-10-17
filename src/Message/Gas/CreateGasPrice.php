<?php

namespace App\Message\Gas;

class CreateGasPrice
{
    /** @var string */
    private $typeId;

    /** @var string */
    private $stationId;

    /** @var string */
    private $date;

    /** @var string */
    private $value;

    public function __construct(string $typeId, string $stationId, string $date, string $value) {
        $this->typeId = $typeId;
        $this->stationId = $stationId;
        $this->date = $date;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getTypeId(): string
    {
        return $this->typeId;
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

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}