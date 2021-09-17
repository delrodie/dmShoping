<?php

namespace App\Controller;

use App\Form\SearchMonitoringType;
use App\Utilities\GestionLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackendMonitoringController extends AbstractController
{
    /**
     * @Route("/backend/monitoring", name="backend_monitoring", methods={"GET","POST"})
     */
    public function index(Request $request, GestionLog $gestionLog): Response
    {
        // Si la date est defini par l'utilisateur alors formatter la date puis rechercher
        $form = $this->createForm(SearchMonitoringType::class);
        $form->handleRequest($request);
        $search = [];
        if ($form->isSubmitted() && $form->isValid()){
            // Sinon prendre la date du jour
            $search = $request->request->get('search_monitoring')['date'];
        }


        if ($search) {
            $date= $search;
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

        return $this->renderForm('backend_monitoring/index.html.twig', [
            'fichiers' => $fichier,
            'date' => $date,
            'form' => $form
        ]);
    }
}
