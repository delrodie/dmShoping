<?php

namespace App\Controller\Admin;

use App\Entity\Localite;
use App\Form\LocaliteType;
use App\Repository\LocaliteRepository;
use App\Utilities\GestionLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/localite")
 */
class BackendLocaliteController extends AbstractController
{
    private $gestionLog;

    public function __construct(GestionLog $gestionLog)
    {
        $this->gestionLog = $gestionLog;
    }

    /**
     * @Route("/", name="backend_localite_index", methods={"GET","POST"})
     */
    public function index(Request $request, LocaliteRepository $localiteRepository): Response
    {
        $localite = new Localite();
        $form = $this->createForm(LocaliteType::class, $localite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($localite);
            $entityManager->flush();

            // Ajout du log
            $this->gestionLog->addLogger($this->log($localite->getLieu())['ajout']);

            $this->addFlash('success', "La localité ".$localite->getLieu()." a bien été enregistré");

            return $this->redirectToRoute('backend_localite_index', [], Response::HTTP_SEE_OTHER);
        }
        // Ajout du log
        $this->gestionLog->addLogger($this->log()['affichage']);

        return $this->renderForm('backend_localite/index.html.twig', [
            'localites' => $localiteRepository->findBy([],['lieu'=>"ASC"]),
            'localite' => $localite,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="backend_localite_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $localite = new Localite();
        $form = $this->createForm(LocaliteType::class, $localite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($localite);
            $entityManager->flush();

            return $this->redirectToRoute('backend_localite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_localite/new.html.twig', [
            'localite' => $localite,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_localite_show", methods={"GET"})
     */
    public function show(Localite $localite): Response
    {
        return $this->render('backend_localite/show.html.twig', [
            'localite' => $localite,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_localite_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Localite $localite, LocaliteRepository $localiteRepository): Response
    {
        $form = $this->createForm(LocaliteType::class, $localite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backend_localite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_localite/edit.html.twig', [
            'localites' => $localiteRepository->findBy([],['lieu'=>"ASC"]),
            'localite' => $localite,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_localite_delete", methods={"POST"})
     */
    public function delete(Request $request, Localite $localite): Response
    {
        if ($this->isCsrfTokenValid('delete'.$localite->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($localite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backend_localite_index', [], Response::HTTP_SEE_OTHER);
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
            'affichage' => $user." a visualisé la liste des localités le ".$date." via l'IP ",
            'ajout' => $user." a enregistré la localité ".$action." le ".$date." via l'IP ",
            'modif' => $user." a modifié la localité ".$action." le ".$date." via l'IP ",
            'sup' => $user." a supprimé la localité ".$action." le ".$date." via l'IP ",
        ];

        return $val;
    }
}
