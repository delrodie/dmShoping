<?php

namespace App\Utilities;

use App\Repository\AlbumRepository;
use App\Repository\PressageRepository;
use Doctrine\ORM\EntityManagerInterface;

class GestionAlbum
{

    private $albumRepository;
    private $pressageRepository;
    private $entityManager;

    public function __construct(AlbumRepository $albumRepository, PressageRepository $pressageRepository, EntityManagerInterface $entityManager)
    {
        $this->albumRepository = $albumRepository;
        $this->pressageRepository = $pressageRepository;
        $this->entityManager = $entityManager;
    }

    public function albumListPressage()
    {
        $pressages = $this->pressageRepository->findList();
        $lists=[]; $i=0;

        foreach ($pressages as $pressage){
            $lists[$i++]=[
                'id' => $pressage->getId(),
                'artiste' => $pressage->getAlbum()->getArtiste()->getNom(),
                'album' => $pressage->getAlbum()->getTitre(),
                'qte' => $pressage->getQuantite(),
                'date' => $pressage->getDate(),
                'stockAlbum' => $pressage->getAlbum()->getStock(),
                'stockFinal' => $pressage->getStockFinal()
            ];
        }

        return $lists;
    }

    /**
     * @param $albumID
     * @param int $qte
     * @param null $pressage
     * @return bool
     */
    public function addStock($albumID, int $qte, $pressage = null)
    {
        $album = $this->albumRepository->findOneBy(['id' => $albumID]);

        if ($album) {
            $stock = (int)$album->getStock() + $qte;
            $album->setStock($stock);

            // Mise a jour de la table pressage
            if ($pressage){
                $pressage->setStockFinal($stock);
            }

            $this->entityManager->flush();

            $response = true;
        }
        else $response = false;

        return $response;
    }

    public function updateStock($albumID, int $qte, $pressage = null)
    {
        $album = $this->albumRepository->findOneBy(['id' => $albumID]);

        if ($album && $pressage){
            $stock = (int)$album->getStock() - $qte;
            $album->setStock($stock);

            // Mise a jour de la table pressage
            if ($pressage){
                $pressage->setStockFinal($stock);
            }

            $this->entityManager->flush();

            $response = true;
        }
        elseif ($album){

            $stock = (int)$album->getStock() - $qte;
            $album->setStock($stock);

            $this->entityManager->flush();

            $response = true;
        }
        else $response = false;

        return $response;
    }

}