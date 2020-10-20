<?php

namespace App\Traits;

use App\Entity\User\User;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy(policy="all")
 */
trait DoctrineEventsTrait
{
    /**
     * @var \DateTime|null
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     *
     * @Serializer\Expose
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     */
    public $createdAt;

    /**
     * @var \DateTime|null
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Serializer\Expose
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     */
    public $updatedAt;

    /**
     * @var User|null
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true))
     */
    public $createdBy;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        if ($this->createdAt) {
            return DateTimeImmutable::createFromMutable($this->createdAt);
        }
        return null;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        if ($this->updatedAt) {
            return DateTimeImmutable::createFromMutable($this->updatedAt);
        }
        return null;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }
}