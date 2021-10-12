<?php

namespace App\Controller;

use App\Entity\Recouvrement;
use App\Form\RecouvrementType;
use App\Repository\RecouvrementRepository;
use App\Utilities\GestionFacture;
use App\Utilities\GestionLog;
use App\Utilities\GestionRecouvrement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/recouvrement")
 */
class BackendRecouvrementController extends AbstractController
{

    private $gestionLog;
    private $gestionRecouvrement;
    private $gestionFacture;

    public function __construct(GestionLog $gestionLog, GestionRecouvrement $gestionRecouvrement, GestionFacture $gestionFacture)
    {
        $this->gestionLog = $gestionLog;
        $this->gestionRecouvrement = $gestionRecouvrement;
        $this->gestionFacture = $gestionFacture;
    }
    
    /**
     * @Route("/", name="backend_recouvrement_index", methods={"GET","POST"})
     */
    public function index(Request $request, RecouvrementRepository $recouvrementRepository): Response
    {
        $recouvrement = new Recouvrement();
        $form = $this->createForm(RecouvrementType::class, $recouvrement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Verification du solde du vendeur
            if (!$this->gestionRecouvrement->verifcationSoldeVendeur($recouvrement->getVendeur(), $recouvrement->getMontant())){
                $this->addFlash('danger', "Echèc: Le montant saisi est supérieur au montant restant à payer");

                return $this->redirectToRoute('backend_recouvrement_index', [], Response::HTTP_SEE_OTHER);
            }

            $recouvrement->setReference($this->gestionRecouvrement->reference());
            $recouvrement->setRestant($recouvrement->getMontant());

            $entityManager->persist($recouvrement);
            $entityManager->flush();

            // Deduction du montant du recouvrement du doit du vendeur
            $this->gestionFacture->vendeurFinance($recouvrement->getVendeur(), $recouvrement->getMontant());

            // Ajout du log
            $action = 'Ref:'.$recouvrement->getReference()." de ".$recouvrement->getVendeur()->getNom();
            $this->gestionLog->addLogger($this->log($action)['ajout']);

            $this->addFlash('success', "Le recouvrement ".$action." a bien été enregistré");

            return $this->redirectToRoute('backend_recouvrement_index', [], Response::HTTP_SEE_OTHER);
        }

        // Ajout du log
        $this->gestionLog->addLogger($this->log()['affichage']);

        return $this->renderForm('backend_recouvrement/index.html.twig', [
            'lists' => $this->gestionRecouvrement->findAllList(),
            'recouvrement' => $recouvrement,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="backend_recouvrement_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $recouvrement = new Recouvrement();
        $form = $this->createForm(RecouvrementType::class, $recouvrement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recouvrement);
            $entityManager->flush();

            return $this->redirectToRoute('backend_recouvrement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_recouvrement/new.html.twig', [
            'recouvrement' => $recouvrement,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_recouvrement_show", methods={"GET"})
     */
    public function show(Recouvrement $recouvrement): Response
    {
        return $this->render('backend_recouvrement/show.html.twig', [
            'recouvrement' => $recouvrement,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_recouvrement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Recouvrement $recouvrement): Response
    {
        $form = $this->createForm(RecouvrementType::class, $recouvrement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ancien_montant = (int)$request->get('ancien_montant');
            $nouveau_montant = (int) $recouvrement->getMontant();

            // Verification du solde du vendeur
            $montant = $nouveau_montant - $ancien_montant;
            if (!$this->gestionRecouvrement->verifcationSoldeVendeur($recouvrement->getVendeur(), $montant)){
                $this->addFlash('danger', "Echèc: Le montant saisi est supérieur au montant restant à payer");

                return $this->redirectToRoute('backend_recouvrement_index', [], Response::HTTP_SEE_OTHER);
            }

            $recouvrement->setRestant($recouvrement->getMontant());

            $this->getDoctrine()->getManager()->flush();

            // Deduction du montant du recouvrement du doit du vendeur
            $nouveauMontant = $ancien_montant - $nouveau_montant;
            $this->gestionFacture->vendeurFinance($recouvrement->getVendeur(), $nouveauMontant, null, true);

            // Ajout du log
            $action = 'Ref:'.$recouvrement->getReference()." de ".$recouvrement->getVendeur()->getNom();
            $this->gestionLog->addLogger($this->log($action)['modif']);

            return $this->redirectToRoute('backend_recouvrement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_recouvrement/edit.html.twig', [
            'lists' => $this->gestionRecouvrement->findAllList(),
            'recouvrement' => $recouvrement,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_recouvrement_delete", methods={"POST"})
     */
    public function delete(Request $request, Recouvrement $recouvrement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recouvrement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $recouvrementEntity = $recouvrement;
            $entityManager->remove($recouvrement);
            $entityManager->flush();

            $this->gestionFacture->vendeurFinance($recouvrement->getVendeur(), $recouvrementEntity->getMontant(), null, true);

            // Ajout du log
            $action = 'Ref:'.$recouvrementEntity->getReference()." destinée à ".$recouvrementEntity->getVendeur()->getNom();
            $this->gestionLog->addLogger($this->log($action)['sup']);
        }

        return $this->redirectToRoute('backend_recouvrement_index', [], Response::HTTP_SEE_OTHER);
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
            'affichage' => $user." a visualisé la liste des recouvrements le ".$date." via l'IP ",
            'ajout' => $user." a enregistré le recouvrement ".$action." le ".$date." via l'IP ",
            'modif' => $user." a modifié le recouvrement ".$action." le ".$date." via l'IP ",
            'sup' => $user." a supprimé le recouvrement ".$action." le ".$date." via l'IP ",
        ];

        return $val;
    }
}
