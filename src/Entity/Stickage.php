<?php

namespace App\Entity;

use App\Repository\StickageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StickageRepository::class)
 */
class Stickage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $quantite;

    /**
     * @ORM\ManyToOne(targetEntity=Album::class)
     */
    private $album;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $stickeFinal;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nonStickeFinal;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): self
    {
        $this->album = $album;

        return $this;
    }

    public function getStickeFinal(): ?int
    {
        return $this->stickeFinal;
    }

    public function setStickeFinal(?int $stickeFinal): self
    {
        $this->stickeFinal = $stickeFinal;

        return $this;
    }

    public function getNonStickeFinal(): ?int
    {
        return $this->nonStickeFinal;
    }

    public function setNonStickeFinal(?int $nonStickeFinal): self
    {
        $this->nonStickeFinal = $nonStickeFinal;

        return $this;
    }
}
