<?php

namespace App\Controller;

use App\Entity\Stickage;
use App\Form\StickageType;
use App\Repository\StickageRepository;
use App\Utilities\GestionAlbum;
use App\Utilities\GestionLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/stickage")
 */
class BackendStickageController extends AbstractController
{

    private $gestionAlbum;
    private $gestionLog;

    public function __construct(GestionAlbum $gestionAlbum, GestionLog $gestionLog)
    {
        $this->gestionAlbum = $gestionAlbum;
        $this->gestionLog = $gestionLog;
    }

    /**
     * @Route("/", name="backend_stickage_index", methods={"GET","POST"})
     */
    public function index(Request $request, StickageRepository $stickageRepository): Response
    {
        $stickage = new Stickage();
        $form = $this->createForm(StickageType::class, $stickage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Si le nombre a stické est supérieur au non stické alors renvoyez faux
            if ((int) $stickage->getQuantite() > (int) $stickage->getAlbum()->getNonSticke()){
                $this->addFlash('danger', "Attention le nombre de CD à sticker est supérieur à celui en stock. Veuillez revoir le nombre non stické en stock.");
                return $this->redirectToRoute('backend_stickage_index');
            }

            $entityManager->persist($stickage);
            $entityManager->flush();

            // Mise a jour de la table album
            $this->gestionAlbum->addStickage($stickage->getAlbum(), $stickage, $stickage->getQuantite());

            // Ajout du log
            $action = 'ID '.$stickage->getId()." de l'album ".$stickage->getAlbum()->getTitre();
            $this->gestionLog->addLogger($this->log($action)['ajout']);

            $this->addFlash('success', "Le stickage ".$action." a bien été enregistré");

            return $this->redirectToRoute('backend_stickage_index', [], Response::HTTP_SEE_OTHER);
        }

        // Ajout du log
        $this->gestionLog->addLogger($this->log()['affichage']);

        return $this->renderForm('backend_stickage/index.html.twig', [
            'lists' => $this->gestionAlbum->albumListStickage(),
            'stickage' => $stickage,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="backend_stickage_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $stickage = new Stickage();
        $form = $this->createForm(StickageType::class, $stickage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($stickage);
            $entityManager->flush();

            return $this->redirectToRoute('backend_stickage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_stickage/new.html.twig', [
            'stickage' => $stickage,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_stickage_show", methods={"GET"})
     */
    public function show(Stickage $stickage): Response
    {
        return $this->render('backend_stickage/show.html.twig', [
            'stickage' => $stickage,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_stickage_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Stickage $stickage): Response
    {
        $form = $this->createForm(StickageType::class, $stickage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Si le nombre a stické est supérieur au non stické alors renvoyez faux
            $ancienneQte = (int) $request->get('_quantite');
            $nouvelleQte = (int) $stickage->getQuantite();
            $stock = (int) $stickage->getAlbum()->getNonSticke() + $ancienneQte;

            if ($stock < $nouvelleQte){
                $this->addFlash('danger', "Attention le nombre de CD à sticker est supérieur à celui en stock. Veuillez revoir le nombre non stické en stock.");
                return $this->redirectToRoute('backend_stickage_edit', ['id'=>$stickage->getId()]);
            } else {
                $nvStock = $stock - $nouvelleQte; //dd($nouvelleQte);
            }
            $this->getDoctrine()->getManager()->flush();

            // Mise a jour de la table album
            $this->gestionAlbum->updateStickage($stickage->getAlbum(), $stickage, $stickage->getQuantite(), $stock, $ancienneQte);

            // Ajout du log
            $action = 'ID '.$stickage->getId()." de l'album ".$stickage->getAlbum()->getTitre();
            $this->gestionLog->addLogger($this->log($action)['modif']);

            $this->addFlash('success', "Le stickage ".$action." a bien été modifié");

            return $this->redirectToRoute('backend_stickage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_stickage/edit.html.twig', [
            'lists' => $this->gestionAlbum->albumListStickage(),
            'stickage' => $stickage,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backend_stickage_delete", methods={"POST"})
     */
    public function delete(Request $request, Stickage $stickage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$stickage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            $albumEntity = $stickage->getAlbum();
            $quantite = $stickage->getQuantite();
            $id = $stickage->getId();

            $entityManager->remove($stickage);
            $entityManager->flush();

            $this->gestionAlbum->deleteStickage($albumEntity, $quantite);

            // Ajout du log
            $action = 'ID '.$id." de l'album ".$albumEntity->getTitre();
            $this->gestionLog->addLogger($this->log($action)['sup']);

            $this->addFlash('success', "Le stickage ".$action." a bien été supprimé");
        }

        return $this->redirectToRoute('backend_stickage_index', [], Response::HTTP_SEE_OTHER);
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
            'affichage' => $user." a visualisé la liste des stickages le ".$date." via l'IP ",
            'ajout' => $user." a enregistré le stickage ".$action." le ".$date." via l'IP ",
            'modif' => $user." a modifié le stickage ".$action." le ".$date." via l'IP ",
            'sup' => $user." a supprimé le stickage ".$action." le ".$date." via l'IP ",
        ];

        return $val;
    }
}
