<?php

namespace App\Entity\Gas;

use App\Command\GasStationGoogleMapCommand;
use App\Entity\Address;
use App\Entity\Google\Place;
use App\Entity\Media;
use App\Entity\Review;
use App\Repository\Gas\StationRepository;
use App\Traits\DoctrineEventsTrait;
use App\Util\FileSystem;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;


/**
 * @ORM\Table(name="gas_station")
 * @ORM\Entity(repositoryClass=StationRepository::class)
 * @ORM\HasLifecycleCallbacks
 *
 * @Serializer\ExclusionPolicy(policy="all")
 */
class Station
{
    use DoctrineEventsTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     *
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     *
     * @Serializer\Expose()
     */
    private $pop;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $name;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $company;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $element;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" : 0})
     *
     * @Serializer\Expose()
     */
    private $isClosed;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $isForced;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $isFormatted;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $isGoogled;

    /**
     * @var ?float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $distanceMatch;

    /**
     * @var ?float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $similarText;

    /**
     * @var ?\DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $closedAt;

    /**
     * @var Address
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Address", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     *
     * @Serializer\Expose()
     */
    private $address;

    /**
     * @var Media
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Media", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="preview_id", referencedColumnName="id", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $preview;

    /**
     * @var Price[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Gas\Price", mappedBy="station", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"date" = "ASC", "type" = "ASC"})
     */
    private $prices;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Gas\Service", mappedBy="stations", cascade={"persist"}, fetch="EXTRA_LAZY")
     *
     * @Serializer\Expose()
     */
    private $services;

    /**
     * @var Place
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Google\Place", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(name="google_place_id", referencedColumnName="id")
     *
     * @Serializer\Expose()
     */
    private $googlePlace;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Review", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="gas_station_reviews")
     *
     * @Serializer\Expose()
     */
    private $reviews;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     *
     * @Serializer\Expose()
     * @Serializer\SerializedName("prices")
     */
    private $lastPrices = [];

    public function __construct(string $id, string $pop, string $postalCode, string $longitude, string $latitude, string $street, string $city, string $country, array $element)
    {
        $this->id = $id;
        $this->pop = $pop;
        $this->element = $element;
        $this->address = new Address($postalCode, $longitude, $latitude, $street, $city, $country);
        $this->googlePlace = new Place();

        $this->isClosedOrNot();
        $this->isForced = false;
        $this->isGoogled = false;
        $this->isFormatted = false;

        $this->prices = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->reviews = new ArrayCollection();
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

    public function getPop(): ?string
    {
        return $this->pop;
    }

    public function setPop(string $pop): self
    {
        $this->pop = $pop;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        if (is_null($name)) {
            $this->name = $name;
        }

        $this->name = ucwords(strtolower(FileSystem::stripAccents($name)));

        return $this;
    }

    public function getElement(): ?array
    {
        return $this->element;
    }

    public function setElement(array $element): self
    {
        $this->element = $element;

        return $this;
    }

    public function getIsClosed(): ?bool
    {
        return $this->isClosed;
    }

    public function setIsClosed(bool $isClosed): self
    {
        $this->isClosed = $isClosed;

        return $this;
    }

    public function getClosedAt(): ?\DateTimeInterface
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeInterface $closedAt): self
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function setStreet(string $street): self
    {
        $this->address->setStreet($street);

        return $this;
    }

    public function setCity(string $city): self
    {
        $this->address->setCity($city);

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getLastPrices(): ?array
    {
        return $this->lastPrices;
    }

    public function setLastPrices(array $lastPrices): self
    {
        $this->lastPrices = $lastPrices;

        return $this;
    }

    public function getIsForced(): ?bool
    {
        return $this->isForced;
    }

    public function setIsForced(bool $isForced): self
    {
        $this->isForced = $isForced;

        return $this;
    }

    public function getIsGoogled(): ?bool
    {
        return $this->isGoogled;
    }

    public function setIsGoogled(bool $isGoogled): self
    {
        $this->isGoogled = $isGoogled;

        return $this;
    }

    public function getIsFormatted(): ?bool
    {
        return $this->isFormatted;
    }

    public function setIsFormatted(bool $isFormatted): self
    {
        $this->isFormatted = $isFormatted;

        return $this;
    }

    public function getDistanceMatch(): ?float
    {
        return $this->distanceMatch;
    }

    public function setDistanceMatch(?float $distanceMatch): self
    {
        $this->distanceMatch = $distanceMatch;

        return $this;
    }

    public function getSimilarText(): ?float
    {
        return $this->similarText;
    }

    public function setSimilarText(?float $similarText): self
    {
        $this->similarText = $similarText;

        return $this;
    }

    public function updateAddress(?array $addressComponents)
    {
        foreach ($addressComponents as $component) {
            foreach ($component['types'] as $type) {
                switch ($type) {
                    case 'street_number':
                        $this->address->setNumber($component['long_name']);
                        break;
                    case 'route':
                        $this->address->setStreet($component['long_name']);
                        break;
                    case 'locality':
                        $this->address->setCity($component['long_name']);
                        break;
                    case 'administrative_area_level_1':
                        $this->address->setRegion($component['long_name']);
                        break;
                    case 'country':
                        $this->address->setCountry($component['long_name']);
                        break;
                    case 'postal_code':
                        $this->address->setPostalCode($component['long_name']);
                        break;
                }
            }
        }

        return $this;
    }

    public function setLongitude($longitude)
    {
        $this->address->setLongitude($longitude);

        return $this;
    }

    public function setLatitude($latitude)
    {
        $this->address->setLatitude($latitude);

        return $this;
    }

    public function setVicinity(?string $vicinity)
    {
        $this->address->setVicinity($vicinity);

        return $this;
    }

    /**
     * @return Collection|Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        $this->reviews->removeElement($review);

        return $this;
    }

    public function getPreview(): ?Media
    {
        return $this->preview;
    }

    public function setPreview(?Media $preview): self
    {
        $this->preview = $preview;

        return $this;
    }
}
