<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass=MediaRepository::class)
 *
 * @Serializer\ExclusionPolicy(policy="all")
 */
class Media
{
    const PUBLIC_GAS_STATION_IMG = "asset/img/gas/station/";
    const GAS_STATION_IMG = [
        'total' => 'total.jpg',
        'esso' => 'esso-express.jpg',
        'shell' => 'shell.jpg',
        'bp' => 'bp.jpg',
        'avia' => 'avia.jpg',
        'intermarche' => 'intermarche.jpg',
        'leclerc' => 'leclerc.jpg',
        'carrefour' => 'carrefour.jpg',
        'auchan' => 'auchan.jpg',
    ];

    /** @var UploadedFile|null */
    private $file;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string")
     *
     * @Serializer\Expose()
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     *
     * @Serializer\Expose()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="mime_type", type="string")
     *
     * @Serializer\Expose()
     */
    private $mimeType;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string")
     *
     * @Serializer\Expose()
     */
    private $type;

    /**
     * @var float
     *
     * @ORM\Column(name="size", type="decimal")
     *
     * @Serializer\Expose()
     */
    private $size;

    public function __construct(string $path, string $name, string $mimeType, string $type, float $size)
    {
        $this->path = $path;
        $this->name = $name;
        $this->mimeType = $mimeType;
        $this->type = $type;
        $this->size = $size;
    }

    public function load(string $path, string $name, string $mimeType, string $type, float $size): self
    {
        $this->path = $path;
        $this->name = $name;
        $this->type = $type;
        $this->mimeType = $mimeType;
        $this->size = $size;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
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

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

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

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }
}
