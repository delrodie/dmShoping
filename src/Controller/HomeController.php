<?php

namespace App\Controller;

use App\Entity\Affiche;
use App\Entity\Album;
use App\Entity\Slide;
use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private $albumRepository;

    public function __construct(AlbumRepository $albumRepository)
    {
        $this->albumRepository = $albumRepository;
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {

        return $this->render('home/index.html.twig', [
            'albums' => $this->albumRepository->findBy(['ecommerce'=>true], ['id'=>'DESC']),
            'promos' => $this->albumRepository->findBy(['promotion'=> true, 'ecommerce' => true], ['id'=>'DESC']),
            'slides' => $this->getDoctrine()->getRepository(Slide::class)->findBy(['statut'=>true], ['id'=>'DESC']),
            'affiches' => $this->getDoctrine()->getRepository(Affiche::class)->findBy(['statut'=>true], ['id'=>'DESC'])
        ]);
    }
}
