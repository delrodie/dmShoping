<?php

namespace App\Controller\Admin;

use App\Entity\Facture;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use App\Utilities\GestionFacture;
use App\Utilities\GestionLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/facture")
 */
class BackendFactureController extends AbstractController
{

    private $gestionLog;
    private $gestionFacture;

    public function __construct(GestionLog $gestionLog, GestionFacture $gestionFacture)
    {
        $this->gestionLog = $gestionLog;
        $this->gestionFacture = $gestionFacture;
    }
    /**
     * @Route("/", name="backend_facture_index", methods={"GET","POST"})
     */
    public function index(Request $request, FactureRepository $factureRepository): Response
    {
        $facture = new Facture();
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Reference
            $facture->setReference($this->gestionFacture->reference());

            $entityManager->persist($facture);
            $entityManager->flush();

            // Ajout du log
            $action = 'Ref:'.$facture->getReference()." destinée à ".$facture->getVendeur()->getNom();
            $this->gestionLog->addLogger($this->log($action)['ajout']);

            $this->addFlash('success', "Le destockage ".$action." a bien été enregistré");

            return $this->redirectToRoute('backend_facture_index', [], Response::HTTP_SEE_OTHER);
        }
        
        // Ajout du log
        $this->gestionLog->addLogger($this->log()['affichage']);

        return $this->renderForm('backend_facture/index.html.twig', [
            'lists' => $this->gestionFacture->factureList(),
            'facture' => $facture,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="backend_facture_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $facture = new Facture();
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($facture);
            $entityManager->flush();

            return $this->redirectToRoute('backend_facture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_facture/new.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_facture_show", methods={"GET"})
     */
    public function show(Facture $facture): Response
    {
        return $this->render('backend_facture/show.html.twig', [
            'facture' => $facture,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_facture_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Facture $facture): Response
    {
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            // Ajout du log
            $action = 'Ref:'.$facture->getReference()." destinée à ".$facture->getVendeur()->getNom();
            $this->gestionLog->addLogger($this->log($action)['modif']);

            return $this->redirectToRoute('backend_facture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_facture/edit.html.twig', [
            'lists' => $this->gestionFacture->factureList(),
            'facture' => $facture,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_facture_delete", methods={"POST"})
     */
    public function delete(Request $request, Facture $facture): Response
    {
        if ($this->isCsrfTokenValid('delete'.$facture->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $factureEntity = $facture;
            $entityManager->remove($facture);
            $entityManager->flush();

            // Ajout du log
            $action = 'Ref:'.$factureEntity->getReference()." destinée à ".$factureEntity->getVendeur()->getNom();
            $this->gestionLog->addLogger($this->log($action)['sup']);
        }

        return $this->redirectToRoute('backend_facture_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Fonction de gestion de log
     *
     * @param null $action
     * @return string[]
     */
    protected function log($action=null)
    {
        $user = $this->getUser()->getUserIdentifier();
        $date = date('Y-m-d H:i:s');
        $val = [
            'affichage' => $user." a visualisé la liste des factures le ".$date." via l'IP ",
            'ajout' => $user." a enregistré la facture ".$action." le ".$date." via l'IP ",
            'modif' => $user." a modifié la facture ".$action." le ".$date." via l'IP ",
            'sup' => $user." a supprimé la facture ".$action." le ".$date." via l'IP ",
        ];

        return $val;
    }
}
