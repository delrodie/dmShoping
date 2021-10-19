<?php

namespace App\Entity;

use App\Repository\EncaissementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EncaissementRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Encaissement
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $montant;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rap;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $qteRestant;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Vente::class)
     */
    private $vente;

    /**
     * @ORM\ManyToOne(targetEntity=Recouvrement::class)
     */
    private $recouvrement;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $flag;

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

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(?int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getRap(): ?int
    {
        return $this->rap;
    }

    public function setRap(?int $rap): self
    {
        $this->rap = $rap;

        return $this;
    }

    public function getQteRestant(): ?int
    {
        return $this->qteRestant;
    }

    public function setQteRestant(?int $qteRestant): self
    {
        $this->qteRestant = $qteRestant;

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

    public function getVente(): ?Vente
    {
        return $this->vente;
    }

    public function setVente(?Vente $vente): self
    {
        $this->vente = $vente;

        return $this;
    }

    public function getRecouvrement(): ?Recouvrement
    {
        return $this->recouvrement;
    }

    public function setRecouvrement(?Recouvrement $recouvrement): self
    {
        $this->recouvrement = $recouvrement;

        return $this;
    }
	
	/**
	 * @ORM\PrePersist
	 */
	public function setCreatedAtValue()
         	{
         		$this->createdAt = new \DateTime();
         	}

    public function getFlag(): ?bool
    {
        return $this->flag;
    }

    public function setFlag(?bool $flag): self
    {
        $this->flag = $flag;

        return $this;
    }
}
