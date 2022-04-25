<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Entity\Vente;
use App\Form\AlbumType;
use App\Repository\AlbumRepository;
use App\Utilities\GestionAlbum;
use App\Utilities\GestionLog;
use App\Utilities\GestionMedia;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/album")
 */
class BackendAlbumController extends AbstractController
{
    private $gestionLog;
    private $gestionMedia;
    private $gestionAlbum;

    public function __construct(GestionLog $gestionLog, GestionMedia $gestionMedia, GestionAlbum $gestionAlbum)
    {
        $this->gestionLog = $gestionLog;
        $this->gestionMedia = $gestionMedia;
        $this->gestionAlbum = $gestionAlbum;
    }

    /**
     * @Route("/", name="backend_album_index", methods={"GET"})
     */
    public function index(AlbumRepository $albumRepository): Response
    {
        // Ajout du log
        $this->gestionLog->addLogger($this->log()['affichage']);

        return $this->render('backend_album/index.html.twig', [
            'albums' => $albumRepository->findList(),
        ]);
    }

    /**
     * @Route("/new", name="backend_album_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($album->getTitre());
            $album->setSlug($slug);

            $mediaFile = $form->get('pochette')->getData(); //dd($mediaFile);

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'album'); //dd($activite->getLogo());

                // Supression de l'ancien fichier
                //$this->gestionMedia->removeUpload($activite->getLogo(), 'activite');

                $album->setPochette($media);
            }

            $album->setReference($this->gestionAlbum->reference($album));
            $entityManager->persist($album);
            $entityManager->flush();

            // Ajout du log
            $this->gestionLog->addLogger($this->log($album->getTitre())['ajout']);

            $this->addFlash('success', "L'album ".$album->getTitre()." a bien été enregistré");

            return $this->redirectToRoute('backend_album_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_album/new.html.twig', [
            'album' => $album,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{slug}", name="backend_album_show", methods={"GET"})
     */
    public function show(Album $album): Response
    {
        return $this->render('backend_album/show.html.twig', [
            'album' => $album,
	        'ventes' => $this->getDoctrine()->getRepository(Vente::class)->findByAlbum($album->getId())
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_album_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Album $album): Response
    {
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($album->getTitre());
            $album->setSlug($slug);


            $mediaFile = $form->get('pochette')->getData(); //dd($mediaFile);

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'album'); //dd($activite->getLogo());

                // Supression de l'ancien fichier
                if ($album->getPochette())
                    $this->gestionMedia->removeUpload($album->getPochette(), 'album');

                $album->setPochette($media);
            }

            $this->getDoctrine()->getManager()->flush();

            // Ajout du log
            $this->gestionLog->addLogger($this->log($album->getTitre())['modif']);

            $this->addFlash('success', "L'album ".$album->getTitre()." a bien été modifié");

            return $this->redirectToRoute('backend_album_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_album/edit.html.twig', [
            'album' => $album,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_album_delete", methods={"POST"})
     */
    public function delete(Request $request, Album $album): Response
    {
        if ($this->isCsrfTokenValid('delete'.$album->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $titre = $album->getTitre();
            $media = $album->getPochette();
            $entityManager->remove($album);
            $entityManager->flush();

            // Supression de l'ancien fichier
            if ($album->getPochette())
                $this->gestionMedia->removeUpload($media, 'album');

            // Ajout du log
            $this->gestionLog->addLogger($this->log($titre)['sup']);

            $this->addFlash('success', "L'album ".$titre." a bien été supprimé");
        }

        return $this->redirectToRoute('backend_album_index', [], Response::HTTP_SEE_OTHER);
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
            'ajout' => $user." a enregistré l'album ".$action." le ".$date." via l'IP ",
            'modif' => $user." a modifié l'album ".$action." le ".$date." via l'IP ",
            'sup' => $user." a supprimé l'album ".$action." le ".$date." via l'IP ",
        ];

        return $val;
    }
}
