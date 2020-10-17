<?php

namespace App\Entity\Gas;

use App\Entity\General\Address;
use App\Entity\Google\Place;
use App\Repository\Gas\StationRepository;
use App\Traits\DoctrineEventsTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="gas_station")
 * @ORM\Entity(repositoryClass=StationRepository::class)
 */
class Station
{
    use DoctrineEventsTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $pop;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $element;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $isClosed;

    /**
     * @var ?\DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $closedAt;

    /**
     * @var Address
     *
     * @ORM\OneToOne(targetEntity="App\Entity\General\Address", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    private $address;

    /**
     * @var Price[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Gas\Price", mappedBy="station", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"date" = "DESC"})
     */
    private $prices;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Gas\Service", mappedBy="stations", cascade={"persist"}, fetch="EXTRA_LAZY")
     */
    private $services;

    /**
     * @var Place
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Google\Place", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(name="google_place_id", referencedColumnName="id")
     */
    private $googlePlace;

    public function __construct(string $id, string $pop, string $postalCode, string $longitude, string $latitude, string $street, string $city, string $country, array $element)
    {
        $this->id = $id;
        $this->pop = $pop;
        $this->element = $element;
        $this->address = new Address($postalCode, $longitude, $latitude, $street, $city, $country);

        $this->isClosedOrNot();

        $this->prices = new ArrayCollection();
        $this->services = new ArrayCollection();
    }

    public function isClosedOrNot()
    {
        $this->isClosed = false;
        $this->closedAt = null;

        if (isset($this->element['fermeture']['attributes']['type']) && "D" == $this->element['fermeture']['attributes']['type']) {
            $this->closedAt = DateTime::createFromFormat('Y-m-d H:i:s', str_replace("T", " ", substr($this->element['fermeture']['attributes']['debut'], 0, 19)));
            $this->isClosed = true;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Price[]
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(Price $price): self
    {
        if (!$this->prices->contains($price)) {
            $this->prices[] = $price;
            $price->setStation($this);
        }

        return $this;
    }

    public function removePrice(Price $price): self
    {
        if ($this->prices->contains($price)) {
            $this->prices->removeElement($price);
            // set the owning side to null (unless already changed)
            if ($price->getStation() === $this) {
                $price->setStation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Service[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->addStation($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
            $service->removeStation($this);
        }

        return $this;
    }

    public function getGooglePlace(): ?Place
    {
        return $this->googlePlace;
    }

    public function setGooglePlace(?Place $googlePlace): self
    {
        $this->googlePlace = $googlePlace;

        return $this;
    }
}
