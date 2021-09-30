<?php

namespace App\Controller;

use App\Entity\Commande;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/impression")
 */
class ImpressionController extends AbstractController
{
    /**
     * @Route("/commande/{reference}", name="impression_commande")
     */
    public function precommande($reference): Response
    {
        $commande = $this->getDoctrine()->getRepository(Commande::class)->findOneBy(['reference'=>$reference]);
        dd($commande);
        return $this->render('impression/facture_precommande.html.twig', [
            'controller_name' => 'ImpressionController',
        ]);
    }
}
