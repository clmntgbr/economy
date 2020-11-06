<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use App\Traits\DoctrineEventsTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=ReviewRepository::class)
 *
 * @Serializer\ExclusionPolicy(policy="all")
 */
class Review
{
    use DoctrineEventsTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"Review:GasStation"})
     */
    private $id;

    /**
     * @var ?string
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"Review:GasStation"})
     */
    private $text;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"Review:GasStation"})
     */
    private $language;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"Review:GasStation"})
     */
    private $rating;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"Review:GasStation"})
     */
    private $authorName;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"Review:GasStation"})
     */
    private $authorURL;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"Review:GasStation"})
     */
    private $profilePhotoUrl;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"Review:GasStation"})
     */
    private $relativeTimeDescription;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"GasStation:Price"})
     */
    private $dateTimestamp;

    /**
     * @var ?\DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"Review:GasStation"})
     */
    private $date;

    /**
     * @var ?User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\Column(nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"Review:GasStation"})
     */
    public $user;

    public function __construct($text, $language, $rating, $dateTimestamp, $authorName, $authorURL, $profilePhotoUrl, $relativeTimeDescription, $user)
    {
        $this->text = $text;
        $this->language = $language;
        $this->rating = $rating;
        $this->dateTimestamp = $dateTimestamp;
        $this->authorName = $authorName;
        $this->authorURL = $authorURL;
        $this->profilePhotoUrl = $profilePhotoUrl;
        $this->relativeTimeDescription = $relativeTimeDescription;
        $this->date = DateTime::createFromFormat('U', $dateTimestamp);
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getRating(): ?string
    {
        return $this->rating;
    }

    public function setRating(?string $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(?string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAuthorName(): ?string
    {
        return $this->authorName;
    }

    public function setAuthorName(?string $authorName): self
    {
        $this->authorName = $authorName;

        return $this;
    }

    public function getAuthorURL(): ?string
    {
        return $this->authorURL;
    }

    public function setAuthorURL(?string $authorURL): self
    {
        $this->authorURL = $authorURL;

        return $this;
    }

    public function getProfilePhotoUrl(): ?string
    {
        return $this->profilePhotoUrl;
    }

    public function setProfilePhotoUrl(?string $profilePhotoUrl): self
    {
        $this->profilePhotoUrl = $profilePhotoUrl;

        return $this;
    }

    public function getRelativeTimeDescription(): ?string
    {
        return $this->relativeTimeDescription;
    }

    public function setRelativeTimeDescription(?string $relativeTimeDescription): self
    {
        $this->relativeTimeDescription = $relativeTimeDescription;

        return $this;
    }

    public function getDateTimestamp(): ?int
    {
        return $this->dateTimestamp;
    }

    public function setDateTimestamp(int $dateTimestamp): self
    {
        $this->dateTimestamp = $dateTimestamp;

        return $this;
    }
}
