<?php

namespace App\Utilities;

use App\Repository\CommandeRepository;
use App\Repository\PrecommandeRepository;
use Doctrine\ORM\EntityManagerInterface;

class GestionCommande
{

    private $entityManager;
    private $precommandeRepository;
    private $commandeRepository;

    public function __construct(EntityManagerInterface $entityManager, PrecommandeRepository $precommandeRepository, CommandeRepository $commandeRepository)
    {
        $this->entityManager = $entityManager;
        $this->precommandeRepository = $precommandeRepository;
        $this->commandeRepository = $commandeRepository;
    }

    public function precommandeListByStatut($statut): array
    {
        $precommandes = $this->precommandeRepository->findByStatut($statut);
        $lists=[]; $i=0;
        foreach ($precommandes as $precommande){
            $lists[$i++] = [
                'id' => $precommande->getId(),
                'reference' => $precommande->getReference(),
                'nom' => $precommande->getNom(),
                'tel' => $precommande->getTel(),
                'adresse' => $precommande->getAdresse(),
                'email' => $precommande->getEmail(),
                'quantite' => $precommande->getQuantite(),
                'montant' => $precommande->getMontant(),
                'flag' => $precommande->getFlag(),
                'idTransaction' => $precommande->getIdTransaction(),
                'statusTransaction' => $precommande->getStatusTransaction(),
                'date' => $precommande->getCreatedAt(),
                'localite_lieu' => $precommande->getLocalite()->getLieu(),
                'localite_prix' => $precommande->getLocalite()->getPrix(),
                'album_titre' => $precommande->getAlbum()->getTitre(),
                'album_prixVente' => $precommande->getAlbum()->getPrixVente(),
                'artiste_nom' => $precommande->getAlbum()->getArtiste()->getNom()
            ];
        }

        return $lists;
    }

    public function commandeListAll(): array
    {
        $commandes = $this->commandeRepository->findListAll();
        $lists=[]; $i=0;
        foreach ($commandes as $precommande){
            $lists[$i++] = [
                'id' => $precommande->getId(),
                'reference' => $precommande->getReference(),
                'nom' => $precommande->getNom(),
                'tel' => $precommande->getTel(),
                'adresse' => $precommande->getAdresse(),
                'email' => $precommande->getEmail(),
                'quantite' => $precommande->getQuantite(),
                'montant' => $precommande->getMontant(),
                'idTransaction' => $precommande->getIdTransaction(),
                'statusTransaction' => $precommande->getStatusTransaction(),
                'telTransaction' => $precommande->getTelTransaction(),
                'date' => $precommande->getCreatedAt(),
                'localite_lieu' => $precommande->getLocalite()->getLieu(),
                'localite_prix' => $precommande->getLocalite()->getPrix(),
                'album_titre' => $precommande->getAlbum()->getTitre(),
                'album_prixVente' => $precommande->getAlbum()->getPrixVente(),
                'artiste_nom' => $precommande->getAlbum()->getArtiste()->getNom(),
                'livraison' => $precommande->getLivraison()
            ];
        }

        return $lists;
    }

}