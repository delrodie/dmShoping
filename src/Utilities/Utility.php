<?php

namespace App\Utilities;

use App\Entity\Artiste;
use Doctrine\ORM\EntityManagerInterface;

class Utility
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return string
     */
    public function matricule(): string
    {
        $matricule = $this->code_aleatoire().''.$this->lettre_aleatoire();

        $exist = $this->entityManager->getRepository(Artiste::class)->findOneBy(['matricule'=>$matricule]);

        while ($exist){
            $matricule = $this->code_aleatoire().''.$this->lettre_aleatoire();
            $exist = $this->entityManager->getRepository(Artiste::class)->findOneBy(['matricule'=>$matricule]);
        }

        return $matricule;
    }

    /**
     * Generation du code aleatoire pour constituer le matricule du scout
     *
     * @return int
     */
    private function code_aleatoire():int
    {
        return mt_rand(1000,9999);
    }

    /**
     * Generation du lettre aleatoire pour constituer le matricule du scout
     * @return string
     */
    private function lettre_aleatoire():string
    {
        // Liste des lettres de l'alphabet
        $alphabet="ABCDEFGHIJKLMNOPQRSTUVWXYZ";

        return $alphabet[rand(0,25)];
    }
}