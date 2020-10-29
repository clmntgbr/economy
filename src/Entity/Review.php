<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use App\Traits\DoctrineEventsTrait;
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

    public function __construct($text, $language, $rating, $date, $user)
    {
        $this->text = $text;
        $this->language = $language;
        $this->rating = $rating;
        $this->date = $date;
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
}
