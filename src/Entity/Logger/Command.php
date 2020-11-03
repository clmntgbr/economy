<?php

namespace App\Entity\Logger;

use App\Repository\Logger\CommandRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="logger_command")
 * @ORM\Entity(repositoryClass=CommandRepository::class)
 */
class Command
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $processus;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $end;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $duration;

    /**
     * @var ?array
     *
     * @ORM\Column(type="array", nullable=true)
     */
    private $result;

    public function __construct(string $processus, string $type, \DateTime $start, \DateTime $end, string $duration, array $result)
    {
        $this->processus = $processus;
        $this->type = $type;
        $this->start = $start;
        $this->end = $end;
        $this->duration = $duration;
        $this->result = $result;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProcessus(): ?string
    {
        return $this->processus;
    }

    public function setProcessus(string $processus): self
    {
        $this->processus = $processus;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getResult(): ?array
    {
        return $this->result;
    }

    public function setResult(?array $result): self
    {
        $this->result = $result;

        return $this;
    }
}
