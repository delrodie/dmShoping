<?php

namespace App\Controller\Admin;

use App\Entity\Facture;
use App\Entity\Vente;
use App\Form\VenteType;
use App\Repository\VenteRepository;
use App\Utilities\GestionAlbum;
use App\Utilities\GestionFacture;
use App\Utilities\GestionLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/vente")
 */
class BackendVenteController extends AbstractController
{

    private $gestionLog;
    private $gestionAlbum;
    private $gestionFacture;

    public function __construct(GestionLog $gestionLog, GestionAlbum $gestionAlbum, GestionFacture $gestionFacture)
    {
        $this->gestionLog = $gestionLog;
        $this->gestionAlbum = $gestionAlbum;
        $this->gestionFacture = $gestionFacture;
    }
    /**
     * @Route("/{reference}", name="backend_vente_index", methods={"GET","POST"})
     */
    public function index(Request $request, VenteRepository $venteRepository, Facture $facture): Response
    {
        $vente = new Vente();
        $form = $this->createForm(VenteType::class, $vente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Si la quantité en vente est suppérieure à la quantité stickée disponible alors echec
            // Sinon continuer l'opération
            if ($vente->getQuantite() > $vente->getAlbum()->getSticke()){
                $this->addFlash('danger', "Echec, la quantité en vente est suppérieure à celle stické pour cet album. Veuillez en faire sticker");
                return $this->redirectToRoute('backend_vente_index',['reference'=>$facture->getReference()]);
            }

            $vente->setFacture($facture);

            $montant = (int) $vente->getPu() * (int) $vente->getQuantite();
            $vente->setMontant($montant);

            $entityManager->persist($vente);
            $entityManager->flush();

            // Mise a jour du nombre de CD stickés
            // Mise a jour de la facture
            $this->gestionAlbum->toggleSticke($vente->getAlbum(),$vente->getQuantite());
            $this->gestionFacture->operation($facture, $vente->getMontant(), true);

            return $this->redirectToRoute('backend_vente_index', ['reference'=>$facture->getReference()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_vente/index.html.twig', [
            'ventes' => $venteRepository->findBy(['facture'=>$facture->getId()]),
            'vente' => $vente,
            'form' => $form,
            'facture'=> $facture
        ]);
    }

    /**
     * @Route("/new", name="backend_vente_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $vente = new Vente();
        $form = $this->createForm(VenteType::class, $vente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($vente);
            $entityManager->flush();

            return $this->redirectToRoute('backend_vente_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_vente/new.html.twig', [
            'vente' => $vente,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_vente_show", methods={"GET"})
     */
    public function show(Vente $vente): Response
    {
        return $this->render('backend_vente/show.html.twig', [
            'vente' => $vente,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_vente_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Vente $vente): Response
    {
        $form = $this->createForm(VenteType::class, $vente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backend_vente_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_vente/edit.html.twig', [
            'vente' => $vente,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_vente_delete", methods={"POST"})
     */
    public function delete(Request $request, Vente $vente): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vente->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($vente);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backend_vente_index', [], Response::HTTP_SEE_OTHER);
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
            'affichage' => $user." a visualisé la liste des albums le ".$date." via l'IP ",
            'ajout' => $user." a enregistré ".$action." le ".$date." via l'IP ",
            'modif' => $user." a modifié ".$action." le ".$date." via l'IP ",
            'sup' => $user." a supprimé ".$action." le ".$date." via l'IP ",
        ];

        return $val;
    }
}
