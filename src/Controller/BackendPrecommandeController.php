<?php

namespace App\Controller;

use App\Entity\Precommande;
use App\Form\Precommande1Type;
use App\Repository\PrecommandeRepository;
use App\Utilities\GestionCommande;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/precommande")
 */
class BackendPrecommandeController extends AbstractController
{
    private $gestionCommande;

    public function __construct(GestionCommande $gestionCommande)
    {
        $this->gestionCommande = $gestionCommande;
    }

    /**
     * @Route("/", name="backend_precommande_index", methods={"GET"})
     */
    public function index(PrecommandeRepository $precommandeRepository): Response
    {
        //dd($this->gestionCommande->precommandeListByStatut('UNKNOW'));
        return $this->render('backend_precommande/index.html.twig', [
            'precommandes' => $this->gestionCommande->precommandeListByStatut('UNKNOW'),
        ]);
    }

    /**
     * @Route("/new", name="backend_precommande_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $precommande = new Precommande();
        $form = $this->createForm(Precommande1Type::class, $precommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($precommande);
            $entityManager->flush();

            return $this->redirectToRoute('backend_precommande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_precommande/new.html.twig', [
            'precommande' => $precommande,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_precommande_show", methods={"GET"})
     */
    public function show(Precommande $precommande): Response
    {
        return $this->render('backend_precommande/show.html.twig', [
            'precommande' => $precommande,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_precommande_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Precommande $precommande): Response
    {
        $form = $this->createForm(Precommande1Type::class, $precommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backend_precommande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_precommande/edit.html.twig', [
            'precommande' => $precommande,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_precommande_delete", methods={"POST"})
     */
    public function delete(Request $request, Precommande $precommande): Response
    {
        if ($this->isCsrfTokenValid('delete'.$precommande->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($precommande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backend_precommande_index', [], Response::HTTP_SEE_OTHER);
    }
}
