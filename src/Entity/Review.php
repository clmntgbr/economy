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

    public function __construct($text, $language, $rating, $dateTimestamp, $authorName, $authorURL, $profilePhotoUrl, $user)
    {
        $this->text = $text;
        $this->language = $language;
        $this->rating = $rating;
        $this->dateTimestamp = $dateTimestamp;
        $this->authorName = $authorName;
        $this->authorURL = $authorURL;
        $this->profilePhotoUrl = $profilePhotoUrl;
        $this->date = DateTime::createFromFormat('U', $dateTimestamp);
        $this->createdBy = $user;
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

    public function getAuthorName(): ?string
    {
        if (is_null($this->createdBy)) {
            return $this->authorName;
        }

        return $this->createdBy->getEmail();
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
        if (is_null($this->createdBy)) {
            return $this->profilePhotoUrl;
        }

        return sprintf('%s%s', $this->createdBy->getAvatar()->getPath(), $this->createdBy->getAvatar()->getName());
    }

    public function setProfilePhotoUrl(?string $profilePhotoUrl): self
    {
        $this->profilePhotoUrl = $profilePhotoUrl;

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
