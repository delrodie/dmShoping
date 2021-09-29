<?php

namespace App\Controller;

use App\Entity\Slide;
use App\Form\SlideType;
use App\Repository\SlideRepository;
use App\Utilities\GestionLog;
use App\Utilities\GestionMedia;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/slide")
 */
class BackendSlideController extends AbstractController
{

    private $gestionMedia;
    private $gestionLog;

    public function __construct(GestionMedia $gestionMedia, GestionLog $gestionLog)
    {
        $this->gestionMedia = $gestionMedia;
        $this->gestionLog = $gestionLog;
    }

    /**
     * @Route("/", name="backend_slide_index", methods={"GET","POST"})
     */
    public function index(Request $request, SlideRepository $slideRepository): Response
    {
        $slide = new Slide();
        $form = $this->createForm(SlideType::class, $slide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($slide->getTitre());
            $slide->setSlug($slug);

            $mediaFile = $form->get('media')->getData(); //dd($mediaFile);

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'slide'); //dd($activite->getLogo());

                // Supression de l'ancien fichier
                //$this->gestionMedia->removeUpload($activite->getLogo(), 'activite');

                $slide->setMedia($media);
            }

            $entityManager->persist($slide);
            $entityManager->flush();

            // Ajout du log
            $this->gestionLog->addLogger($this->log($slide->getTitre())['ajout']);

            $this->addFlash('success', "Le slide ".$slide->getTitre()." a bien été enregistré");

            return $this->redirectToRoute('backend_slide_index', [], Response::HTTP_SEE_OTHER);
        }

        // Ajout du log
        $this->gestionLog->addLogger($this->log()['affichage']);

        return $this->renderForm('backend_slide/index.html.twig', [
            'slides' => $slideRepository->findBy([],['id'=>"DESC"]),
            'slide' => $slide,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="backend_slide_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $slide = new Slide();
        $form = $this->createForm(SlideType::class, $slide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($slide);
            $entityManager->flush();

            return $this->redirectToRoute('backend_slide_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_slide/new.html.twig', [
            'slide' => $slide,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_slide_show", methods={"GET"})
     */
    public function show(Slide $slide): Response
    {
        return $this->render('backend_slide/show.html.twig', [
            'slide' => $slide,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_slide_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Slide $slide, SlideRepository $slideRepository): Response
    {
        $form = $this->createForm(SlideType::class, $slide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($slide->getTitre());
            $slide->setSlug($slug);

            $mediaFile = $form->get('media')->getData(); //dd($mediaFile);

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'slide'); //dd($activite->getLogo());

                // Supression de l'ancien fichier
                if ($slide->getMedia())
                    $this->gestionMedia->removeUpload($slide->getMedia(), 'slide');

                $slide->setMedia($media);
            }

            $this->getDoctrine()->getManager()->flush();

            // Ajout du log
            $this->gestionLog->addLogger($this->log($slide->getTitre())['modif']);

            $this->addFlash('success', "Le slide ".$slide->getTitre()." a bien été modifié");

            return $this->redirectToRoute('backend_slide_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('backend_slide/edit.html.twig', [
            'slides' => $slideRepository->findBy([],['id'=>"DESC"]),
            'slide' => $slide,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_slide_delete", methods={"POST"})
     */
    public function delete(Request $request, Slide $slide): Response
    {
        if ($this->isCsrfTokenValid('delete'.$slide->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $slideEntity = $slide;
            $entityManager->remove($slide);
            $entityManager->flush();

            // Supression de l'ancien fichier
            if ($slideEntity->getMedia())
                $this->gestionMedia->removeUpload($slideEntity->getMedia(), 'slide');

            // Ajout du log
            $this->gestionLog->addLogger($this->log($slideEntity->getTitre())['sup']);

            $this->addFlash('success', "Le slide ".$slideEntity->getTitre()." a bien été supprimé");
        }

        return $this->redirectToRoute('backend_slide_index', [], Response::HTTP_SEE_OTHER);
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
            'affichage' => $user." a visualisé la liste des slides le ".$date." via l'IP ",
            'ajout' => $user." a enregistré le slide ".$action." le ".$date." via l'IP ",
            'modif' => $user." a modifié le slide ".$action." le ".$date." via l'IP ",
            'sup' => $user." a supprimé le slide ".$action." le ".$date." via l'IP ",
        ];

        return $val;
    }
}
