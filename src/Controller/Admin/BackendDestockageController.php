<?php

namespace App\Controller\Admin;

use App\Entity\Destockage;
use App\Form\DestockageType;
use App\Repository\DestockageRepository;
use App\Utilities\GestionAlbum;
use App\Utilities\GestionLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/destockage")
 */
class BackendDestockageController extends AbstractController
{
    private $gestionAlbum;
    private $gestionLog;

    public function __construct(GestionAlbum $gestionAlbum, GestionLog $gestionLog)
    {
        $this->gestionAlbum = $gestionAlbum;
        $this->gestionLog = $gestionLog;
    }

    /**
     * @Route("/", name="backend_destockage_index", methods={"GET","POST"})
     */
    public function index(Request $request, DestockageRepository $destockageRepository): Response
    {
        $destockage = new Destockage();
        $form = $this->createForm(DestockageType::class, $destockage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Si la quantité a destocker est suppérieure à la quantité disponible alors echec
            // Sinon continuer l'opération
            if ($destockage->getQuantite() > $destockage->getAlbum()->getSticke()){
                $this->addFlash('danger', "Echec, la quantité à destocker est suppérieure à celle stické pour cet album. Veuillez en faire sticker");
                return $this->redirectToRoute('backend_destockage_index');
            }
            $entityManager->persist($destockage);
            $entityManager->flush();

            // Mise a jour de la table Album
            $this->gestionAlbum->toggleSticke($destockage->getAlbum(), $destockage->getQuantite());

            // Ajout du log
            $action = 'ID '.$destockage->getId()." de l'album ".$destockage->getAlbum()->getTitre();
            $this->gestionLog->addLogger($this->log($action)['ajout']);

            $this->addFlash('success', "Le destockage ".$action." a bien été enregistré");

            return $this->redirectToRoute('backend_destockage_index', [], Response::HTTP_SEE_OTHER);
        }

        // Ajout du log
        $this->gestionLog->addLogger($this->log()['affichage']);

        return $this->renderForm('backend_destockage/index.html.twig', [
            'lists' => $this->gestionAlbum->albumListDestickage(),
            'destockage' => $destockage,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="backend_destockage_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $destockage = new Destockage();
        $form = $this->createForm(DestockageType::class, $destockage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($destockage);
            $entityManager->flush();

            return $this->redirectToRoute('backend_destockage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_destockage/new.html.twig', [
            'destockage' => $destockage,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_destockage_show", methods={"GET"})
     */
    public function show(Destockage $destockage): Response
    {
        return $this->render('backend_destockage/show.html.twig', [
            'destockage' => $destockage,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_destockage_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Destockage $destockage): Response
    {
        $form = $this->createForm(DestockageType::class, $destockage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ancienneQte = $request->get('_quantite');
            $nouvelleQte = $destockage->getQuantite();
            $difference = (int) $ancienneQte - (int)$nouvelleQte;

            $this->getDoctrine()->getManager()->flush();

            // Mise a jour de la table Album
            $this->gestionAlbum->toggleSticke($destockage->getAlbum(), $difference, true);

            // Ajout du log
            $action = 'ID '.$destockage->getId()." de l'album ".$destockage->getAlbum()->getTitre();
            $this->gestionLog->addLogger($this->log($action)['modif']);

            $this->addFlash('success', "Le destockage ".$action." a bien été modifié");

            return $this->redirectToRoute('backend_destockage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_destockage/edit.html.twig', [
            'lists' => $this->gestionAlbum->albumListDestickage(),
            'destockage' => $destockage,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_destockage_delete", methods={"POST"})
     */
    public function delete(Request $request, Destockage $destockage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$destockage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            // Variable
            $quantite = $destockage->getQuantite();
            $album = $destockage->getAlbum();
            $destockageID = $destockage->getId();

            $entityManager->remove($destockage);
            $entityManager->flush();

            // Mise a jour de la table Album
            $this->gestionAlbum->toggleSticke($album, $quantite, true);

            // Ajout du log
            $action = 'ID '.$destockageID." de l'album ".$album->getTitre();
            $this->gestionLog->addLogger($this->log($action)['sup']);

            $this->addFlash('success', "Le destockage ".$action." a bien été supprimé");
        }

        return $this->redirectToRoute('backend_destockage_index', [], Response::HTTP_SEE_OTHER);
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
            'affichage' => $user." a visualisé la liste des CD destockés le ".$date." via l'IP ",
            'ajout' => $user." a enregistré le destockage ".$action." le ".$date." via l'IP ",
            'modif' => $user." a modifié le destockage ".$action." le ".$date." via l'IP ",
            'sup' => $user." a supprimé le destockage ".$action." le ".$date." via l'IP ",
        ];

        return $val;
    }
}
