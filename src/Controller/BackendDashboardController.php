<?php

namespace App\Controller;

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
    /**
     * @Route("/", name="backend_dashboard")
     */
    public function index(Request $request, GestionLog $log): Response
    {
        //Ajout du log
        $str = $this->log()['affichage'];
        $log->addLogger($str);

        return $this->render('backend_dashboard/index.html.twig', [
            'controller_name' => 'BackendDashboardController',
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
