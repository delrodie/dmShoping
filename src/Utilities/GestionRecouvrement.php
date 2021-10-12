<?php

namespace App\Utilities;

use App\Entity\Recouvrement;
use App\Repository\RecouvrementRepository;
use Doctrine\ORM\EntityManagerInterface;

class GestionRecouvrement
{
    private $entityManager;
    private $recouvrementRepository;

    public function __construct(EntityManagerInterface $entityManager, RecouvrementRepository $recouvrementRepository)
    {
        $this->entityManager = $entityManager;
        $this->recouvrementRepository = $recouvrementRepository;
    }

    /**
     * Liste des recouvrement par ordre decroissance selon la date
     *
     * @return array
     */
    public function findAllList(): array
    {
        $recouvrements = $this->recouvrementRepository->findListe();

        $lists=[]; $i=0;
        foreach($recouvrements as $recouvrement){
            $lists[$i++]=[
                'id' => $recouvrement->getId(),
                'vendeur' => $recouvrement->getVendeur()->getNom(),
                'montant' => $recouvrement->getMontant(),
                'reference' => $recouvrement->getReference(),
                'date' => $recouvrement->getDate(),
                'flag' => $recouvrement->getFlag(),
            ];
        }

        return $lists;
    }

    /**
     * Verification du solde restant après opération actuelle
     *
     * @param object $vendeur
     * @param int $montant
     * @return bool
     */
    public function verifcationSoldeVendeur(object $vendeur, int $montant): bool
    {
        if ($montant > $vendeur->getReste())
            return false;
        else{
            return true;
        }
    }


    /**
     * Generation de la reference de la facture
     *
     * @return string
     */
    public function reference(): string
    {
        $nombre = count($this->entityManager->getRepository(Recouvrement::class)->findAll())+1;
        $res = 0;
        if ($nombre < 10) $res = '000'.$nombre;
        elseif ($nombre < 100) $res = '00'.$nombre;
        elseif ($nombre < 1000) $res = '0'.$nombre;
        else $res = $nombre;

        $reference = $res.'DMR'.date('my');

        return $reference;
    }
}