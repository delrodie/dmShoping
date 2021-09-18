<?php

namespace App\Controller;

use App\Entity\Pressage;
use App\Form\PressageType;
use App\Repository\PressageRepository;
use App\Utilities\GestionAlbum;
use App\Utilities\GestionLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/pressage")
 */
class BackendPressageController extends AbstractController
{

    private $gestionAlbum;
    private $gestionLog;

    public function __construct(GestionAlbum $gestionAlbum, GestionLog $gestionLog)
    {
        $this->gestionAlbum = $gestionAlbum;
        $this->gestionLog = $gestionLog;
    }
    /**
     * @Route("/", name="backend_pressage_index", methods={"GET","POST"})
     */
    public function index(Request $request, PressageRepository $pressageRepository): Response
    {
        $pressage = new Pressage();
        $form = $this->createForm(PressageType::class, $pressage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($pressage);
            $entityManager->flush();

            // Augmentation du sotck de l'album concerné
            $this->gestionAlbum->addStock($pressage->getAlbum()->getId(),$pressage->getQuantite(), $pressage);

            // Ajout du log
            $action = 'ID '.$pressage->getId()." de l'album ".$pressage->getAlbum()->getTitre();
            $this->gestionLog->addLogger($this->log($action)['ajout']);

            $this->addFlash('success', "Le pressage ".$action." a bien été enregistré");

            return $this->redirectToRoute('backend_pressage_index', [], Response::HTTP_SEE_OTHER);
        }

        // Ajout du log
        $this->gestionLog->addLogger($this->log()['affichage']);

        return $this->renderForm('backend_pressage/index.html.twig', [
            'lists' => $this->gestionAlbum->albumListPressage(),
            'pressage' => $pressage,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="backend_pressage_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $pressage = new Pressage();
        $form = $this->createForm(PressageType::class, $pressage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pressage);
            $entityManager->flush();

            return $this->redirectToRoute('backend_pressage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_pressage/new.html.twig', [
            'pressage' => $pressage,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_pressage_show", methods={"GET"})
     */
    public function show(Pressage $pressage): Response
    {
        return $this->render('backend_pressage/show.html.twig', [
            'pressage' => $pressage,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_pressage_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Pressage $pressage): Response
    {
        $form = $this->createForm(PressageType::class, $pressage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ancienStock = (int)$request->get('_quantite');
            $this->getDoctrine()->getManager()->flush();

            // Augmentation du sotck de l'album concerné
            $this->gestionAlbum->addStock($pressage->getAlbum()->getId(),$pressage->getQuantite(), $pressage);
            $this->gestionAlbum->updateStock($pressage->getAlbum()->getId(),$ancienStock, $pressage);

            // Ajout du log
            $action = 'ID '.$pressage->getId()." de l'album ".$pressage->getAlbum()->getTitre();
            $this->gestionLog->addLogger($this->log($action)['modif']);

            $this->addFlash('success', "Le pressage ".$action." a bien été enregistré");

            return $this->redirectToRoute('backend_pressage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_pressage/edit.html.twig', [
            'lists' => $this->gestionAlbum->albumListPressage(),
            'pressage' => $pressage,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_pressage_delete", methods={"POST"})
     */
    public function delete(Request $request, Pressage $pressage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pressage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            // Sauvegarde des information
            $quantite = $pressage->getQuantite();
            $album = $pressage->getAlbum();
            $id = $pressage->getId();

            $entityManager->remove($pressage);
            $entityManager->flush();

            // Reduction du stock
            $this->gestionAlbum->updateStock($album->getId(), $quantite);

            // Ajout du log
            $action = 'ID '.$id." de l'album ".$album->getTitre();
            $this->gestionLog->addLogger($this->log($action)['sup']);

            $this->addFlash('success', "Le pressage ".$action." a bien été enregistré");
        }

        return $this->redirectToRoute('backend_pressage_index', [], Response::HTTP_SEE_OTHER);
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
            'affichage' => $user." a visualisé la liste des pressages le ".$date." via l'IP ",
            'ajout' => $user." a enregistré le pressage ".$action." le ".$date." via l'IP ",
            'modif' => $user." a modifié le pressage ".$action." le ".$date." via l'IP ",
            'sup' => $user." a supprimé le pressage ".$action." le ".$date." via l'IP ",
        ];

        return $val;
    }
}
