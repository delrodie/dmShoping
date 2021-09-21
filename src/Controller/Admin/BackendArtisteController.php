<?php

namespace App\Controller\Admin;

use App\Entity\Artiste;
use App\Form\ArtisteType;
use App\Repository\ArtisteRepository;
use App\Utilities\GestionLog;
use App\Utilities\GestionMedia;
use App\Utilities\Utility;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/artiste")
 */
class BackendArtisteController extends AbstractController
{
    private $gestionLog;
    private $gestionMedia;
    private $utility;

    public function __construct(GestionLog $gestionLog, GestionMedia $gestionMedia, Utility $utility)
    {
        $this->gestionLog = $gestionLog;
        $this->gestionMedia = $gestionMedia;
        $this->utility = $utility;
    }

    /**
     * @Route("/", name="backend_artiste_index", methods={"GET", "POST"})
     */
    public function index(Request $request, ArtisteRepository $artisteRepository): Response
    {
        $artiste = new Artiste();
        $form = $this->createForm(ArtisteType::class, $artiste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($artiste->getNom());
            $artiste->setSlug($slug);


            $mediaFile = $form->get('media')->getData(); //dd($mediaFile);

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'artiste'); //dd($activite->getLogo());

                // Supression de l'ancien fichier
                //$this->gestionMedia->removeUpload($activite->getLogo(), 'activite');

                $artiste->setMedia($media);
            }

            $artiste->setMatricule($this->utility->matricule());

            $entityManager->persist($artiste);
            $entityManager->flush();

            // Ajout du log
            $this->gestionLog->addLogger($this->log($artiste->getNom())['ajout']);

            $this->addFlash('success', "L'artiste ".$artiste->getNom()." a bien été enregistré");


            return $this->redirectToRoute('backend_artiste_index', [], Response::HTTP_SEE_OTHER);
        }

        // Ajout du log
        $this->gestionLog->addLogger($this->log()['affichage']);

        return $this->renderForm('backend_artiste/index.html.twig', [
            'artistes' => $artisteRepository->findBy([],['nom'=>"ASC"]),
            'artiste' => $artiste,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="backend_artiste_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $artiste = new Artiste();
        $form = $this->createForm(ArtisteType::class, $artiste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($artiste);
            $entityManager->flush();

            return $this->redirectToRoute('backend_artiste_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_artiste/new.html.twig', [
            'artiste' => $artiste,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_artiste_show", methods={"GET"})
     */
    public function show(Artiste $artiste): Response
    {
        return $this->render('backend_artiste/show.html.twig', [
            'artiste' => $artiste,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_artiste_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Artiste $artiste, ArtisteRepository $artisteRepository): Response
    {
        $form = $this->createForm(ArtisteType::class, $artiste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($artiste->getNom());
            $artiste->setSlug($slug);


            $mediaFile = $form->get('media')->getData(); //dd($mediaFile);

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'artiste'); //dd($activite->getLogo());

                // Supression de l'ancien fichier
                if ($artiste->getMedia())
                    $this->gestionMedia->removeUpload($artiste->getMedia(), 'artiste');

                $artiste->setMedia($media);
            }

            $this->getDoctrine()->getManager()->flush();

            // Ajout du log
            $this->gestionLog->addLogger($this->log($artiste->getNom())['modif']);

            $this->addFlash('success', "L'artiste ".$artiste->getNom()." a bien été modifié");

            return $this->redirectToRoute('backend_artiste_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_artiste/edit.html.twig', [
            'artistes' => $artisteRepository->findBy([],['nom'=>"ASC"]),
            'artiste' => $artiste,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_artiste_delete", methods={"POST"})
     */
    public function delete(Request $request, Artiste $artiste): Response
    {
        if ($this->isCsrfTokenValid('delete'.$artiste->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            // Sauvegarde
            $media = $artiste->getMedia();
            $nom = $artiste->getNom();

            $entityManager->remove($artiste);
            $entityManager->flush();

            // Suppression du média si celui-ci existe
            if ($media)
                $this->gestionMedia->removeUpload($artiste->getMedia(), 'artiste');

            // Ajout du log
            $this->gestionLog->addLogger($this->log($nom)['sup']);

            $this->addFlash('success', "L'artiste ".$nom." a bien été supprimé");
        }

        return $this->redirectToRoute('backend_artiste_index', [], Response::HTTP_SEE_OTHER);
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
            'affichage' => $user." a visualisé la liste des artistes le ".$date." via l'IP ",
            'ajout' => $user." a enregistré l'artiste ".$action." le ".$date." via l'IP ",
            'modif' => $user." a modifié l'artiste ".$action." le ".$date." via l'IP ",
            'sup' => $user." a supprimé l'artiste ".$action." le ".$date." via l'IP ",
        ];

        return $val;
    }
}
