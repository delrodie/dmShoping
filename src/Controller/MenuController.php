<?php

namespace App\Controller;

use App\Entity\Genre;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/menu")
 */
class MenuController extends AbstractController
{
    /**
     * @Route("/", name="menu_horizontal")
     */
    public function index(): Response
    {
        return $this->render('menu/index.html.twig', [
            'genres' => $this->listGenre(),
        ]);
    }

    /**
     * @Route("/mobile", name="menu_mobile")
     */
    public function mobile(): Response
    {
        return $this->render('menu/mobile.html.twig',[
            'genres' => $this->listGenre(),
        ]);
    }

    /**
     * @Route("/desktop/vertical", name="menu_vertical")
     */
    public function vertical()
    {
        return $this->render('menu/vertical.html.twig',[
            'genres' => $this->listGenre(),
        ]);
    }

    /**
     * @return array
     */
    protected function ListGenre(): array
    {
        return $this->getDoctrine()->getRepository(Genre::class)->findBy([],['libelle'=>"ASC"]);
    }
}
