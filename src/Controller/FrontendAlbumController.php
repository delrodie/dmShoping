<?php

namespace App\Controller;

use App\Entity\Album;
use App\Utilities\GestionAlbum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/albums")
 */
class FrontendAlbumController extends AbstractController
{

    private $gestionAlbum;

    public function __construct(GestionAlbum $gestionAlbum)
    {
        $this->gestionAlbum = $gestionAlbum;
    }

    /**
     * @Route("/", name="frontend_album")
     */
    public function index(): Response
    {
        return $this->render('frontend_album/index.html.twig', [
            'controller_name' => 'FrontendAlbumController',
        ]);
    }

    /**
     * @Route("/{artiste}/{slug}", name="frontend_album_show", methods={"GET"})
     */
    public function show($slug)
    {
        return $this->render('frontend_album/show.html.twig',[
            'album' => $this->gestionAlbum->albumShowBySlug($slug)
        ]);
    }
}
