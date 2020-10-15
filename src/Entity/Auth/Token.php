<?php

namespace App\Entity\Auth;

use App\Repository\Auth\TokenRepository;
use App\Traits\DoctrineEventsTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TokenRepository::class)
 */
class Token
{
    use DoctrineEventsTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $userId;

    /**
     * @ORM\Column(type="text")
     */
    private $jwtHash;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expireAt;

    public function update(string $userId, string $jwtHash, \DateTime $expireAt)
    {
        $this->userId = $userId;
        $this->jwtHash = $jwtHash;
        $this->expireAt = $expireAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getJwtHash(): ?string
    {
        return $this->jwtHash;
    }

    public function setJwtHash(string $jwtHash): self
    {
        $this->jwtHash = $jwtHash;

        return $this;
    }

    public function getExpireAt(): ?\DateTimeInterface
    {
        return $this->expireAt;
    }

    public function setExpireAt(\DateTimeInterface $expireAt): self
    {
        $this->expireAt = $expireAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
