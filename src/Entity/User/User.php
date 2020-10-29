<?php

namespace App\Entity\User;

use App\Entity\Gas\Station;
use App\Repository\User\UserRepository;
use App\Traits\DoctrineEventsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 *
 * @UniqueEntity(fields={"email"}, groups={"User:Register"})
 *
 * @Serializer\ExclusionPolicy(policy="all")
 */
class User implements UserInterface
{
    use DoctrineEventsTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"User"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     *
     * @Assert\NotBlank(groups={"User:Register", "User:Authentication"})
     * @Assert\Email(groups={"User:Register", "User:Authentication"})
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"User"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"User"})
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(groups={"User:Register", "User:Authentication"})
     * @Assert\NotCompromisedPassword(groups={"User:Register"})
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"User"})
     */
    private $isActive;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Gas\Station")
     * @ORM\JoinTable(name="user_like_gas_stations")
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"User"})
     */
    private $gasStationsLikes;

    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->isActive = true;
        $this->gasStationsLikes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection|Station[]
     */
    public function getGasStationsLikes(): Collection
    {
        return $this->gasStationsLikes;
    }

    public function addGasStationsLike(Station $gasStationsLike): self
    {
        if (!$this->gasStationsLikes->contains($gasStationsLike)) {
            $this->gasStationsLikes[] = $gasStationsLike;
        }

        return $this;
    }

    public function gasStationLike(Station $station): self
    {
        if ($this->gasStationsLikes->contains($station)) {
            $this->removeGasStationsLike($station);
            return $this;
        }

        $this->addGasStationsLike($station);
        return $this;
    }

    public function removeGasStationsLike(Station $gasStationsLike): self
    {
        $this->gasStationsLikes->removeElement($gasStationsLike);

        return $this;
    }
}
