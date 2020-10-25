<?php

namespace App\Entity\Google;

use App\Repository\Google\PlaceRepository;
use App\Traits\DoctrineEventsTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Table(name="google_place")
 * @ORM\Entity(repositoryClass=PlaceRepository::class)
 *
 * @Serializer\ExclusionPolicy(policy="all")
 */
class Place
{
    use DoctrineEventsTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $googleId;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $url;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $website;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $phoneNumber;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true, unique=true)
     *
     * @Serializer\Expose()
     */
    private $placeId;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $compoundCode;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $globalCode;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $googleRating;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $reference;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $userRatingsTotal;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $icon;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $businessStatus;

    /**
     * @var array
     *
     * @ORM\Column(type="array", nullable=true)
     */
    private $nearbysearch;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): self
    {
        $this->googleId = $googleId;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getPlaceId(): ?string
    {
        return $this->placeId;
    }

    public function setPlaceId(?string $placeId): self
    {
        $this->placeId = $placeId;

        return $this;
    }

    public function getCompoundCode(): ?string
    {
        return $this->compoundCode;
    }

    public function setCompoundCode(?string $compoundCode): self
    {
        $this->compoundCode = $compoundCode;

        return $this;
    }

    public function getGlobalCode(): ?string
    {
        return $this->globalCode;
    }

    public function setGlobalCode(?string $globalCode): self
    {
        $this->globalCode = $globalCode;

        return $this;
    }

    public function getGoogleRating(): ?string
    {
        return $this->googleRating;
    }

    public function setGoogleRating(?string $googleRating): self
    {
        $this->googleRating = $googleRating;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getUserRatingsTotal(): ?string
    {
        return $this->userRatingsTotal;
    }

    public function setUserRatingsTotal(?string $userRatingsTotal): self
    {
        $this->userRatingsTotal = $userRatingsTotal;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getBusinessStatus(): ?string
    {
        return $this->businessStatus;
    }

    public function setBusinessStatus(?string $businessStatus): self
    {
        $this->businessStatus = $businessStatus;

        return $this;
    }

    public function getNearbysearch(): ?array
    {
        return $this->nearbysearch;
    }

    public function setNearbysearch(?array $nearbysearch): self
    {
        $this->nearbysearch = $nearbysearch;

        return $this;
    }
}
