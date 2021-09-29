<?php

namespace App\Controller;

use App\Entity\Album;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/produit")
 */
class FrontendProduitController extends AbstractController
{
    /**
     * @Route("/", name="frontend_produit_index")
     */
    public function index(): Response
    {
        return $this->render('frontend_produit/index.html.twig', [
            'controller_name' => 'FrontendProduitController',
        ]);
    }

    /**
     * @Route("/{slug}", name="frontend_produit_show", methods={"GET"})
     */
    public function show(Album $album)
    {
        return $this->render('frontend_produit/show.html.twig',[
            'album' => $album,
        ]);
    }
}
