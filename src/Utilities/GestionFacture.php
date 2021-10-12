<?php

namespace App\Utilities;

use App\Entity\Facture;
use App\Entity\Vente;
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

    /**
     * Generation de la reference de la facture
     *
     * @return string
     */
    public function reference(): string
    {
        $nombre = count($this->entityManager->getRepository(Facture::class)->findAll())+1;
        $res = 0;
        if ($nombre < 10) $res = '000'.$nombre;
        elseif ($nombre < 100) $res = '00'.$nombre;
        elseif ($nombre < 1000) $res = '0'.$nombre;
        else $res = $nombre;

        $reference = $res.'DMP'.date('my');

        return $reference;
    }

    /**
     * @param object $facture
     * @param int $montant
     * @param bool|null $vente
     * @return bool
     */
    public function operation(object $facture, int $montant, bool $vente=null): bool
    {
        // Affecter une valeur a TVA selon le type de facture
        if ($facture->getTVA()) $tva = 1.18;
        else $tva = 1;

        // Calcul des differents type de montant
        if ($vente){
            $ttc = (int) $facture->getTtc() + ($montant*$tva);
            $ht = (int) $facture->getMontant() + $montant;
        }else{
            $ttc = (int) $facture->getTtc() - ($montant*$tva);
            $ht = (int) $facture->getMontant() - $montant;
        }

        // Mise a jour de la table
        $facture->setTtc($ttc);
        $facture->setMontant($ht);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Mise a jour des finances du vendeur
     *
     * @param object $vendeur
     * @param int $montant
     * @param bool|null $achat
     * @return bool
     */
    public function vendeurFinance(object $vendeur, int $montant, bool $achat=null, bool $recouvrement=null): bool
    {
        //variables
        $credit = (int) $vendeur->getCredit();
        $reste = (int) $vendeur->getReste();
        $avance = (int) $vendeur->getPayer();

        if ($achat){
            $credit = $credit + $montant;
            $reste = $reste + $montant;
        }elseif ($recouvrement){
            $avance = $avance - $montant;
            $reste = $reste + $montant;
        }
        else{
            $avance = $avance + $montant;
            $reste = $reste - $montant;
        }

        $vendeur->setCredit($credit);
        $vendeur->setReste($reste);
        $vendeur->setPayer($avance);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param $facture
     * @return array
     */
    public function listVente($facture): array
    {
        // Recuperer la liste des ventes concernées par la factures
        // Puis constituer à l'affectation au libellé
        $ventes = $this->entityManager->getRepository(Vente::class)->findByFacture($facture); //dd($ventes);

        $lists = []; $i=0; $qte=0; $totalQte=0; $montantTotal=0; $puTotal=0;
        foreach ($ventes as $vente){
            $totalQte = (int)$vente->getQuantite() + $totalQte;
            $montantTotal = (int) $vente->getMontant() + $montantTotal;
            $puTotal = (int) $vente->getPu() + $puTotal;
            $lists[$i++] = [
                'artiste' => $vente->getAlbum()->getArtiste()->getNom(),
                'album' => $vente->getAlbum()->getTitre(),
                'albumReference' => $vente->getAlbum()->getReference(),
                'ventePU' => $vente->getPu(),
                'venteMontant' => $vente->getMontant(),
                'venteQte' => $vente->getQuantite(),
            ];
        }

        $ventes = [
            'lists' => $lists,
            'totalQte' => $totalQte,
            'montantTotal' => $montantTotal,
            'puTotal' => $puTotal
        ];

        return $ventes;

    }
}