<?php

namespace App\Utilities;

use App\Entity\Encaissement;
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
	
	public function getEncaissementByRecouvrement($recouvrement)
	{
		$encaissements = $this->entityManager->getRepository(Encaissement::class)->findByRecouvrement($recouvrement);
		$list=[]; $i=0;
		foreach ($encaissements as $encaissement) {
			$list[$i++]=[
				'album' => $encaissement->getVente()->getAlbum()->getTitre(),
				'prix_unitaire' => $encaissement->getVente()->getPu(),
				'quantite' => $encaissement->getQuantite(),
				'montant' => $encaissement->getMontant(),
				'rap' => $encaissement->getRap(),
				'qte_restant' => $encaissement->getQteRestant(),
				'artiste' => $encaissement->getVente()->getAlbum()->getArtiste()->getNom()
			];
		}
		
		return $list;
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
	
	/**
	 * @param object $vente
	 * @param int $montant
	 * @return bool
	 */
	public function majVente(object $vente, int $montant): bool
	{
		$avance = (int) $vente->getAvance() + $montant;
		$reste = (int) $vente->getReste() - $montant;
		$vente->setAvance($avance);
		$vente->setReste($reste);
		
		$this->entityManager->flush();
		
		return true;
	}
	
	/**
	 * @param object $recouvrement
	 * @param int $montant
	 * @return bool
	 */
	public function majRecouvrement(object $recouvrement, int $montant): bool
	{
		$reste = (int) $recouvrement->getRestant() - $montant;
		$recouvrement->setRestant($reste);
		
		$this->entityManager->flush();
		
		return true;
	}
}
