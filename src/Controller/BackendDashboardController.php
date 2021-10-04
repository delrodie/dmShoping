<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use App\Repository\ArtisteRepository;
use App\Utilities\GestionAlbum;
use App\Utilities\GestionLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/dashboard")
 */
class BackendDashboardController extends AbstractController
{

    private $artisteRepository;
    private $albumRepository;
    private $gestionAlbum;

    public function __construct(ArtisteRepository $artisteRepository, AlbumRepository $albumRepository, GestionAlbum $gestionAlbum)
    {
        $this->artisteRepository = $artisteRepository;
        $this->albumRepository = $albumRepository;
        $this->gestionAlbum = $gestionAlbum;
    }
    /**
     * @Route("/", name="backend_dashboard")
     */
    public function index(Request $request, GestionLog $log): Response
    {
        //Ajout du log
        $str = $this->log()['affichage'];
        $log->addLogger($str);

        return $this->render('backend_dashboard/index.html.twig', [
            'nombre_artiste' => count($this->artisteRepository->findAll()),
            'albums' => $this->gestionAlbum->albumListAll(),
            'statistiques' => $this->gestionAlbum->statistiques(),
        ]);
    }

    protected function log()
    {
        $user = $this->getUser()->getUserIdentifier();
        $date = date('Y-m-d H:i:s');
        $val = [
            'affichage' => $user." s'est connecté(e) au tableau de bord le ".$date." via l'IP ",
        ];

        return $val;
    }
}
