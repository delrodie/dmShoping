<?php

namespace App\Entity;

use App\Repository\PressageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PressageRepository::class)
 */
class Pressage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $quantite;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Album::class)
     */
    private $album;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $stockFinal;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): self
    {
        $this->date = $date;

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

    public function getStockFinal(): ?int
    {
        return $this->stockFinal;
    }

    public function setStockFinal(?int $stockFinal): self
    {
        $this->stockFinal = $stockFinal;

        return $this;
    }
}
