<?php

namespace App\Utilities;

use App\Entity\Facture;
use Doctrine\ORM\EntityManagerInterface;

class GestionFacture
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function factureList(): array
    {
        $factures = $this->entityManager->getRepository(Facture::class)->findList();
        $lists=[]; $i=0;

        foreach ($factures as $facture){
            $lists[$i++]=[
                'id' => $facture->getId(),
                'vendeur' => $facture->getVendeur()->getNom(),
                'montant' => $facture->getMontant(),
                'TVA' => $facture->getTVA(),
                'TTC' => $facture->getTTC(),
                'reference' => $facture->getReference(),
                'flag' => $facture->getFlag(),
                'date' => $facture->getDate()
            ];
        }

        return $lists;
    }

    public function reference()
    {
        $nombre = count($this->entityManager->getRepository(Facture::class)->findAll())+1;
        $res = 0;
        if ($nombre < 10) $res = '000'.$nombre;
        elseif ($nombre < 100) $res = '00'.$nombre;
        elseif ($nombre < 1000) $res = '0'.$nombre;
        else $res = $nombre;

        $reference = 'F'.date('ym').'-'.$res;

        return $reference;
    }
}