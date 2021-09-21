<?php

namespace App\Utilities;

use App\Repository\AlbumRepository;
use App\Repository\DestockageRepository;
use App\Repository\PressageRepository;
use App\Repository\StickageRepository;
use Doctrine\ORM\EntityManagerInterface;

class GestionAlbum
{

    private $albumRepository;
    private $pressageRepository;
    private $entityManager;
    private $stickageRepository;
    private $destockageRepository;

    public function __construct(
        AlbumRepository $albumRepository,
        PressageRepository $pressageRepository,
        EntityManagerInterface $entityManager,
        StickageRepository $stickageRepository,
        DestockageRepository $destockageRepository
    )
    {
        $this->albumRepository = $albumRepository;
        $this->pressageRepository = $pressageRepository;
        $this->entityManager = $entityManager;
        $this->stickageRepository = $stickageRepository;
        $this->destockageRepository = $destockageRepository;
    }

    /**
     * @return array
     */
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
            $nonSticke = (int)$album->getNonSticke() + $qte;
            $album->setStock($stock);
            $album->setNonSticke($nonSticke);

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
            $nonSticke = (int) $album->getNonSticke() - $qte;
            $album->setStock($stock);
            $album->setNonSticke($nonSticke);

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

    /**
     *  GESTION DU STICKAGE
     */

    public function albumListStickage()
    {
        $stickages = $this->stickageRepository->findList();
        $lists=[]; $i=0;

        foreach ($stickages as $stickage){
            $lists[$i++]=[
                'id' => $stickage->getId(),
                'artiste' => $stickage->getAlbum()->getArtiste()->getNom(),
                'album' => $stickage->getAlbum()->getTitre(),
                'qte' => $stickage->getQuantite(),
                'date' => $stickage->getDate(),
                'stickeFinal' => $stickage->getStickeFinal(),
                'nonStickeFinal' => $stickage->getNonStickeFinal()
            ];
        }

        return $lists;
    }

    public function addStickage($album, $stickage, int $qte)
    {
        $sticke = (int) $album->getSticke() + $qte;
        $nonSticke = (int) $album->getNonSticke() - $qte;

        $album->setSticke($sticke);
        $album->setNonSticke($nonSticke);
        $stickage->setStickeFinal($sticke);
        $stickage->setNonStickeFinal($nonSticke);

        $this->entityManager->flush();

        return true;
    }

    public function updateStickage($album, $stickage, int $qte, int $ancienStock, int $ancienStick)
    {
        $nonSticke = $ancienStock - $qte;
        $ancienSticke = (int) $album->getSticke() - $ancienStick;
        $nouveauSticke = $ancienSticke + $qte;

        $album->setSticke($nouveauSticke);
        $album->setNonSticke($nonSticke);
        $stickage->setStickeFinal($nouveauSticke);
        $stickage->setNonStickeFinal($nonSticke);

        $this->entityManager->flush();

        return true;
    }

    public function deleteStickage($album, int $qte): bool
    {
        $nouveauNonSticke = (int) $album->getNonSticke() + $qte;
        $nouveauSticke = (int) $album->getSticke() - $qte;

        $album->setNonSticke($nouveauNonSticke);
        $album->setSticke($nouveauSticke);

        $this->entityManager->flush();

        return true;
    }

    /**
     * Gestion des opérations sur la quantité stickée
     *
     * @param $album
     * @param int $qte
     * @param null $increase
     * @return bool
     */
    public function toggleSticke($album, int $qte, $increase = null): bool
    {
        // Si fonction ajout actif alors augmenter la quantité
        // Sinon la réduire
        if ($increase)
            $sticke = (int) $album->getSticke() + $qte;
        else
            $sticke = (int) $album->getSticke() - $qte;

        $album->setSticke($sticke);
        $this->entityManager->flush();

        return true;
    }

    public function albumListDestickage()
    {
        $stickages = $this->destockageRepository->findListDestockage();
        $lists=[]; $i=0;

        foreach ($stickages as $stickage){
            $lists[$i++]=[
                'id' => $stickage->getId(),
                'artiste' => $stickage->getAlbum()->getArtiste()->getNom(),
                'album' => $stickage->getAlbum()->getTitre(),
                'qte' => $stickage->getQuantite(),
                'date' => $stickage->getDate(),
                'motif' => $stickage->getMotif(),
            ];
        }

        return $lists;
    }

}