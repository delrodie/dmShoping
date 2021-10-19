<?php

namespace App\Controller;

use App\Entity\Encaissement;
use App\Entity\Recouvrement;
use App\Form\EncaissementType;
use App\Repository\EncaissementRepository;
use App\Utilities\GestionRecouvrement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/encaissement")
 */
class BackendEncaissementController extends AbstractController
{
	private $_recouvrement;
	
	public function __construct(GestionRecouvrement $_recouvrement)
	{
		$this->_recouvrement = $_recouvrement;
	}
	
    /**
     * @Route("/{recouvrement}", name="backend_encaissement_index", methods={"GET","POST"})
     */
    public function index(Request $request, EncaissementRepository $encaissementRepository, Recouvrement $recouvrement): Response
    {
	    $encaissement = new Encaissement();
	    $form = $this->createForm(EncaissementType::class, $encaissement, ['vendeur'=>$recouvrement->getVendeur()->getId()]);
	    $form->handleRequest($request);
	
	    if ($form->isSubmitted() && $form->isValid()) {
		    $entityManager = $this->getDoctrine()->getManager();
			
			// Affecter le recouvrement a l'encaissement
		    $encaissement->setRecouvrement($recouvrement);
		    // Calculer le montant restant ainsi que la quantité
		    $restant = $encaissement->getVente()->getReste();
			if ($encaissement->getMontant() > $restant) {
				$this->addFlash('danger', "Echec! le montant saisi est supérieur au montant restant");
				return $this->redirectToRoute('backend_encaissement_index',['recouvrement'=>$recouvrement->getReference()]);
			}
			$quantite = (int) $encaissement->getMontant() / (int) $encaissement->getVente()->getPu();
			$qteRestant = (int) $encaissement->getVente()->getQuantite() - $quantite;
			$rap = (int) $encaissement->getVente()->getReste() - $encaissement->getMontant();
			$encaissement->setQuantite($quantite);
			$encaissement->setQteRestant($qteRestant);
			$encaissement->setRap($rap);
			
		    $entityManager->persist($encaissement);
		    $entityManager->flush();
		
		    // Mise a jour du recouvrement et vente
		    $this->_recouvrement->majRecouvrement($recouvrement, $encaissement->getMontant());
		    $this->_recouvrement->majVente($encaissement->getVente(), $encaissement->getMontant());
			
			$this->addFlash('success', "L'encaissement a bien été ajouté");
		
		    return $this->redirectToRoute('backend_encaissement_index', ['recouvrement'=>$recouvrement->getReference()], Response::HTTP_SEE_OTHER);
	    }
		
        return $this->renderForm('backend_encaissement/index.html.twig', [
            'encaissements' => $encaissementRepository->findByRecouvrement($recouvrement->getId()),
	        'encaissement' => $encaissement,
	        'form' => $form,
	        'recouvrement' => $recouvrement
        ]);
    }

    /**
     * @Route("/new", name="backend_encaissement_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $encaissement = new Encaissement();
        $form = $this->createForm(EncaissementType::class, $encaissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($encaissement);
            $entityManager->flush();

            return $this->redirectToRoute('backend_encaissement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_encaissement/new.html.twig', [
            'encaissement' => $encaissement,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_encaissement_show", methods={"GET"})
     */
    public function show(Encaissement $encaissement): Response
    {
        return $this->render('backend_encaissement/show.html.twig', [
            'encaissement' => $encaissement,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_encaissement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Encaissement $encaissement): Response
    {
        $form = $this->createForm(EncaissementType::class, $encaissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backend_encaissement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_encaissement/edit.html.twig', [
            'encaissement' => $encaissement,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_encaissement_delete", methods={"POST"})
     */
    public function delete(Request $request, Encaissement $encaissement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$encaissement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($encaissement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backend_encaissement_index', [], Response::HTTP_SEE_OTHER);
    }
}
