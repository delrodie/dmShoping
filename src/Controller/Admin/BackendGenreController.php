<?php

namespace App\Controller\Admin;

use App\Entity\Genre;
use App\Form\GenreType;
use App\Repository\GenreRepository;
use App\Utilities\GestionLog;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/genre")
 */
class BackendGenreController extends AbstractController
{
    private $gestionLog;

    public function __construct(GestionLog $gestionLog)
    {
        $this->gestionLog = $gestionLog;
    }

    /**
     * @Route("/", name="backend_genre_index", methods={"GET","POST"})
     */
    public function index(Request $request, GenreRepository $genreRepository): Response
    {
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($genre->getLibelle());
            $genre->setSlug($slug);

            $entityManager->persist($genre);
            $entityManager->flush();

            // Ajout du log
            $this->gestionLog->addLogger($this->log($genre->getLibelle())['ajout']);

            $this->addFlash('success', "Le genre musical ".$genre->getLibelle()." a bien été enregistré");

            return $this->redirectToRoute('backend_genre_index', [], Response::HTTP_SEE_OTHER);
        }

        // Ajout du log
        $this->gestionLog->addLogger($this->log()['affichage']);

        return $this->renderForm('backend_genre/index.html.twig', [
            'genres' => $genreRepository->findBy([],['libelle'=>"ASC"]),
            'genre' => $genre,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="backend_genre_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($genre);
            $entityManager->flush();

            return $this->redirectToRoute('backend_genre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_genre/new.html.twig', [
            'genre' => $genre,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_genre_show", methods={"GET"})
     */
    public function show(Genre $genre): Response
    {
        return $this->render('backend_genre/show.html.twig', [
            'genre' => $genre,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_genre_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Genre $genre, GenreRepository $genreRepository): Response
    {
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($genre->getLibelle());
            $genre->setSlug($slug);

            $this->getDoctrine()->getManager()->flush();

            // Ajout du log
            $this->gestionLog->addLogger($this->log($genre->getLibelle())['modif']);

            $this->addFlash('success', "Le genre musical ".$genre->getLibelle()." a bien été modifé");

            return $this->redirectToRoute('backend_genre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_genre/edit.html.twig', [
            'genres' => $genreRepository->findBy([],['libelle'=>"ASC"]),
            'genre' => $genre,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_genre_delete", methods={"POST"})
     */
    public function delete(Request $request, Genre $genre): Response
    {
        if ($this->isCsrfTokenValid('delete'.$genre->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $libelle = $genre->getLibelle();
            $entityManager->remove($genre);
            $entityManager->flush();

            // Ajout du log
            $this->gestionLog->addLogger($this->log($genre->getLibelle())['sup']);

            $this->addFlash('success', "Le genre musical ".$libelle." a bien été supprimé");
        }

        return $this->redirectToRoute('backend_genre_index', [], Response::HTTP_SEE_OTHER);
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
            'affichage' => $user." a visualisé la liste du genre musical le ".$date." via l'IP ",
            'ajout' => $user." a enregistré le genre musical ".$action." le ".$date." via l'IP ",
            'modif' => $user." a modifié le genre musical ".$action." le ".$date." via l'IP ",
            'sup' => $user." a supprimé le  genre musical ".$action." le ".$date." via l'IP ",
        ];

        return $val;
    }
}
