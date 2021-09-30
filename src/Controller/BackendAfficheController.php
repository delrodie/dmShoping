<?php

namespace App\Controller;

use App\Entity\Affiche;
use App\Form\AfficheType;
use App\Repository\AfficheRepository;
use App\Utilities\GestionLog;
use App\Utilities\GestionMedia;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/affiche")
 */
class BackendAfficheController extends AbstractController
{

    private $gestionMedia;
    private $gestionLog;

    public function __construct(GestionMedia $gestionMedia, GestionLog $gestionLog)
    {
        $this->gestionMedia = $gestionMedia;
        $this->gestionLog = $gestionLog;
    }

    /**
     * @Route("/", name="backend_affiche_index", methods={"GET","POST"})
     */
    public function index(Request $request, AfficheRepository $afficheRepository): Response
    {
        $affiche = new Affiche();
        $form = $this->createForm(AfficheType::class, $affiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($affiche->getTitre());
            $affiche->setSlug($slug);

            $mediaFile = $form->get('media')->getData(); //dd($mediaFile);

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'affiche'); //dd($activite->getLogo());

                // Supression de l'ancien fichier
                //$this->gestionMedia->removeUpload($activite->getLogo(), 'activite');

                $affiche->setMedia($media);
            }

            $entityManager->persist($affiche);
            $entityManager->flush();

            // Ajout du log
            $this->gestionLog->addLogger($this->log($affiche->getTitre())['ajout']);

            $this->addFlash('success', "L'affiche ".$affiche->getTitre()." a bien été enregistrée");

            return $this->redirectToRoute('backend_affiche_index', [], Response::HTTP_SEE_OTHER);
        }

        // Ajout du log
        $this->gestionLog->addLogger($this->log()['affichage']);

        return $this->renderForm('backend_affiche/index.html.twig', [
            'affiches' => $afficheRepository->findBy([],['id'=>"DESC"]),
            'affiche' => $affiche,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="backend_affiche_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $affiche = new Affiche();
        $form = $this->createForm(AfficheType::class, $affiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($affiche);
            $entityManager->flush();

            return $this->redirectToRoute('backend_affiche_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_affiche/new.html.twig', [
            'affiche' => $affiche,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_affiche_show", methods={"GET"})
     */
    public function show(Affiche $affiche): Response
    {
        return $this->render('backend_affiche/show.html.twig', [
            'affiche' => $affiche,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_affiche_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Affiche $affiche): Response
    {
        $form = $this->createForm(AfficheType::class, $affiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backend_affiche_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_affiche/edit.html.twig', [
            'affiche' => $affiche,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_affiche_delete", methods={"POST"})
     */
    public function delete(Request $request, Affiche $affiche): Response
    {
        if ($this->isCsrfTokenValid('delete'.$affiche->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($affiche);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backend_affiche_index', [], Response::HTTP_SEE_OTHER);
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
            'affichage' => $user." a visualisé la liste des affiche le ".$date." via l'IP ",
            'ajout' => $user." a enregistré l'affiche ".$action." le ".$date." via l'IP ",
            'modif' => $user." a modifié l'affiche ".$action." le ".$date." via l'IP ",
            'sup' => $user." a supprimé l'affiche ".$action." le ".$date." via l'IP ",
        ];

        return $val;
    }
}
