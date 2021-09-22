<?php

namespace App\Controller\Admin;

use App\Entity\Vendeur;
use App\Form\VendeurType;
use App\Repository\VendeurRepository;
use App\Utilities\GestionLog;
use App\Utilities\Utility;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/vendeur")
 */
class BackendVendeurController extends AbstractController
{

    private $gestionLog;
    private $utility;

    public function __construct(GestionLog $gestionLog, Utility $utility)
    {
        $this->gestionLog = $gestionLog;
        $this->utility = $utility;
    }

    /**
     * @Route("/", name="backend_vendeur_index", methods={"GET","POST"})
     */
    public function index(Request $request, VendeurRepository $vendeurRepository): Response
    {
        $vendeur = new Vendeur();
        $form = $this->createForm(VendeurType::class, $vendeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($vendeur->getNom());
            $vendeur->setSlug($slug);

            $vendeur->setCode($this->utility->codeVendeur());

            $entityManager->persist($vendeur);
            $entityManager->flush();

            // Ajout du log
            $this->gestionLog->addLogger($this->log($vendeur->getNom())['ajout']);

            $this->addFlash('success', "Le vendeur ".$vendeur->getNom()." a bien été enregistré");

            return $this->redirectToRoute('backend_vendeur_index', [], Response::HTTP_SEE_OTHER);
        }

        // Ajout du log
        $this->gestionLog->addLogger($this->log()['affichage']);

        return $this->renderForm('backend_vendeur/index.html.twig', [
            'vendeurs' => $vendeurRepository->findBy([],['nom'=>"ASC"]),
            'vendeur' => $vendeur,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="backend_vendeur_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $vendeur = new Vendeur();
        $form = $this->createForm(VendeurType::class, $vendeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($vendeur);
            $entityManager->flush();

            return $this->redirectToRoute('backend_vendeur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_vendeur/new.html.twig', [
            'vendeur' => $vendeur,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_vendeur_show", methods={"GET"})
     */
    public function show(Vendeur $vendeur): Response
    {
        return $this->render('backend_vendeur/show.html.twig', [
            'vendeur' => $vendeur,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_vendeur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Vendeur $vendeur, VendeurRepository $vendeurRepository): Response
    {
        $form = $this->createForm(VendeurType::class, $vendeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($vendeur->getNom());
            $vendeur->setSlug($slug);

            $this->getDoctrine()->getManager()->flush();

            // Ajout du log
            $this->gestionLog->addLogger($this->log($vendeur->getNom())['modif']);

            $this->addFlash('success', "Le vendeur ".$vendeur->getNom()." a bien été modifié");

            return $this->redirectToRoute('backend_vendeur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_vendeur/edit.html.twig', [
            'vendeurs' => $vendeurRepository->findBy([],['nom'=>"ASC"]),
            'vendeur' => $vendeur,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_vendeur_delete", methods={"POST"})
     */
    public function delete(Request $request, Vendeur $vendeur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vendeur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            $vendeurEntity = $vendeur;

            $entityManager->remove($vendeur);
            $entityManager->flush();

            // Ajout du log
            $this->gestionLog->addLogger($this->log($vendeurEntity->getNom())['sup']);

            $this->addFlash('success', "Le vendeur ".$vendeurEntity->getNom()." a bien été supprimé");
        }

        return $this->redirectToRoute('backend_vendeur_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Fonction de gestion de log
     *
     * @param null $action
     * @return string[]
     */
    protected function log($action=null): array
    {
        $user = $this->getUser()->getUserIdentifier();
        $date = date('Y-m-d H:i:s');
        $val = [
            'affichage' => $user." a visualisé la liste des vendeurs le ".$date." via l'IP ",
            'ajout' => $user." a enregistré le vendeur ".$action." le ".$date." via l'IP ",
            'modif' => $user." a modifié le vendeur ".$action." le ".$date." via l'IP ",
            'sup' => $user." a supprimé le vendeur ".$action." le ".$date." via l'IP ",
        ];

        return $val;
    }
}
