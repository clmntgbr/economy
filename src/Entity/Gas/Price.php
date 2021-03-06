<?php

namespace App\Entity\Gas;

use App\Repository\Gas\PriceRepository;
use App\Traits\DoctrineEventsTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Table(name="gas_price")
 * @ORM\Entity(repositoryClass=PriceRepository::class)
 *
 * @Serializer\ExclusionPolicy(policy="all")
 */
class Price
{
    use DoctrineEventsTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"GasStation:Price", "Ajax:GasStation"})
     */
    private $id;

    /**
     * @var Type
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Gas\Type", inversedBy="prices", fetch="EXTRA_LAZY", cascade={"persist"})
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"GasStation:Price", "Ajax:GasStation"})
     */
    public $type;

    /**
     * @var Station
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Gas\Station", inversedBy="prices", fetch="EXTRA_LAZY", cascade={"persist"})
     */
    public $station;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"GasStation:Price", "Ajax:GasStation"})
     */
    private $value;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"GasStation:Price"})
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     *
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"GasStation:Price"})
     */
    private $dateTimestamp;

    public function __construct(Type $type, Station $station, string $value, string $date)
    {
        $this->type = $type;
        $this->station = $station;
        $this->value = (float)$value;
        $this->date = DateTime::createFromFormat('Y-m-d H:i:s', str_replace("T", " ", substr($date, 0, 19)));
        $this->dateTimestamp = $this->date->getTimestamp();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

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

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStation(): ?Station
    {
        return $this->station;
    }

    public function setStation(?Station $station): self
    {
        $this->station = $station;

        return $this;
    }
}
