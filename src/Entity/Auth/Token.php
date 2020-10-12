<?php

namespace App\Entity\Auth;

use App\Repository\Auth\TokenRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TokenRepository::class)
 */
class Token
{
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

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function update(string $userId, string $jwtHash, \DateTime $createdAt, \DateTime $expireAt)
    {
        $this->userId = $userId;
        $this->jwtHash = $jwtHash;
        $this->createdAt = $createdAt;
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
