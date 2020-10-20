<?php

namespace App\Entity\Gas;

use App\Repository\Gas\ServiceRepository;
use App\Traits\DoctrineEventsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Table(name="gas_service")
 * @ORM\Entity(repositoryClass=ServiceRepository::class)
 *
 * @Serializer\ExclusionPolicy(policy="all")
 */
class Service
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
     * @var string
     *
     * @ORM\Column(type="string")
     *
     * @Serializer\Expose()
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Gas\Station", inversedBy="services")
     * @ORM\JoinTable(name="gas_station_services")
     */
    private $stations;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->stations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Station[]
     */
    public function getStations(): Collection
    {
        return $this->stations;
    }

    public function addStation(Station $station): self
    {
        if (!$this->stations->contains($station)) {
            $this->stations[] = $station;
        }

        return $this;
    }

    public function removeStation(Station $station): self
    {
        if ($this->stations->contains($station)) {
            $this->stations->removeElement($station);
        }

        return $this;
    }
}
