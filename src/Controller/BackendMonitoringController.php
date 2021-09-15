<?php

namespace App\Controller;

use App\Utilities\GestionLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackendMonitoringController extends AbstractController
{
    /**
     * @Route("/backend/monitoring", name="backend_monitoring")
     */
    public function index(Request $request, GestionLog $gestionLog): Response
    {
        // Si la date est defini par l'utilisateur alors formatter la date puis rechercher
        // Sinon prendre la date du jour
        $search = $request->get('search');

        if ($search) {
            $form = explode('/', $search);
            $date = $form[0].'-'.$form[1].'-'.$form[2];
        }
        else $date = date('Y-m-d');

        // Appel de la fonction d'ouverture du fichier log
        $fichier = $gestionLog->monitoring($date);

        // Si le fichier n'existe pas alors renvoyer valeur null
        // sinon encoder le fichier en json puis render à la vue
        if (!$fichier) $fichier = null;
        else{
            foreach ($fichier as $item => $value){
                $jsons[] = json_decode($value);
            }
            $fichier = $jsons;
        }

        return $this->render('backend_monitoring/index.html.twig', [
            'fichiers' => $fichier,
            'date' => $date,
        ]);
    }
}
