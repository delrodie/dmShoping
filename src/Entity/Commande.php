<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Commande
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
    private $reference;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $quantite;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $montant;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $idTransaction;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statusTransaction;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telTransaction;

    /**
     * @ORM\ManyToOne(targetEntity=Album::class)
     */
    private $album;

    /**
     * @ORM\ManyToOne(targetEntity=Localite::class)
     */
    private $localite;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dateTransaction;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $timeTransaction;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $livraison;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(?int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getIdTransaction(): ?string
    {
        return $this->idTransaction;
    }

    public function setIdTransaction(?string $idTransaction): self
    {
        $this->idTransaction = $idTransaction;

        return $this;
    }

    public function getStatusTransaction(): ?string
    {
        return $this->statusTransaction;
    }

    public function setStatusTransaction(?string $statusTransaction): self
    {
        $this->statusTransaction = $statusTransaction;

        return $this;
    }

    public function getTelTransaction(): ?string
    {
        return $this->telTransaction;
    }

    public function setTelTransaction(?string $telTransaction): self
    {
        $this->telTransaction = $telTransaction;

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

    public function getLocalite(): ?Localite
    {
        return $this->localite;
    }

    public function setLocalite(?Localite $localite): self
    {
        $this->localite = $localite;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDateTransaction(): ?string
    {
        return $this->dateTransaction;
    }

    public function setDateTransaction(?string $dateTransaction): self
    {
        $this->dateTransaction = $dateTransaction;

        return $this;
    }

    public function getTimeTransaction(): ?string
    {
        return $this->timeTransaction;
    }

    public function setTimeTransaction(?string $timeTransaction): self
    {
        $this->timeTransaction = $timeTransaction;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue(): \DateTime
    {
        return $this->createdAt = new \DateTime();
    }

    public function getLivraison(): ?bool
    {
        return $this->livraison;
    }

    public function setLivraison(?bool $livraison): self
    {
        $this->livraison = $livraison;

        return $this;
    }
}
