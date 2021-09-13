<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/dashboard")
 */
class BackendDashboardController extends AbstractController
{
    /**
     * @Route("/", name="backend_dashboard")
     */
    public function index(): Response
    {
        return $this->render('backend_dashboard/index.html.twig', [
            'controller_name' => 'BackendDashboardController',
        ]);
    }
}
