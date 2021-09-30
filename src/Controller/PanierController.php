<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Commande;
use App\Entity\Localite;
use App\Entity\Precommande;
use App\Form\PrecommandeType;
use App\Repository\AlbumRepository;
use App\Repository\LocaliteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/panier")
 */
class PanierController extends AbstractController
{
    private $albumRepository;
    private $localiteRepository;

    public function __construct(AlbumRepository $albumRepository, LocaliteRepository $localiteRepository)
    {
        $this->albumRepository = $albumRepository;
        $this->localiteRepository = $localiteRepository;
    }

    /**
     * @Route("/", name="panier", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
        $quantite = $request->get('quantite');
        $albumSlug = $request->get('album');

        $commande = new Precommande();
        $form = $this->createForm(PrecommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();

            dd($commande);
        }

        $localites = $this->localiteRepository->findBy(['statut'=>true]);


        return $this->renderForm('panier/index.html.twig', [
            'album' => $this->albumRepository->findOneBy(['slug'=>$albumSlug]),
            'qte' => $quantite,
            'commande' => $commande,
            'form' => $form,
            'localites' => $localites
        ]);
    }

    /**
     * @Route("/ajax", name="requete_ajax_panier", methods={"GET","POST"})
     */
    public function ajax(Request $request)
    {
        //Initialisation
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $field = $request->get('field');
        $value = $request->get('value');

        $frais = (int) $this->getDoctrine()->getRepository(Localite::class)->findOneBy(['id'=>$value])->getPrix();

        return $this->json($frais);
    }

    /**
     * @Route("/commande/validation", name="panier_commande_validation", methods={"GET","POST"})
     */
    public function validation(Request $request)
    {
        //Initialisation
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders); //dd($request);

        $entityManager = $this->getDoctrine()->getManager();

        // Recuperation des informations transmises
        $nom= strtoupper($this->validForm($request->get('precommande')['nom']));
        $tel= strtoupper($this->validForm($request->get('precommande')['tel']));
        $adresse= $this->validForm($request->get('precommande')['adresse']);
        $email= $this->validForm($request->get('precommande')['email']);
        $localiteID= strtoupper($this->validForm($request->get('panier_localite')));
        $quantite= strtoupper($this->validForm($request->get('quantite')));
        $montant= strtoupper($this->validForm($request->get('montant')));
        $albumSlug= $this->validForm($request->get('album'));

        $localite = $this->localiteRepository->findOneBy(['id' => $localiteID]);
        $album = $this->albumRepository->findOneBy(['slug' => $albumSlug]);

        // Enregistrement de la Precommande
        $precommande = new Precommande();
        $precommande->setReference(time());
        $precommande->setNom($nom);
        $precommande->setTel($tel);
        $precommande->setAdresse($adresse);
        $precommande->setEmail($email);
        $precommande->setQuantite($quantite);
        $precommande->setMontant($montant);
        $precommande->setIdTransaction(time());
        $precommande->setStatusTransaction('UNKNOW');
        $precommande->setAlbum($album);
        $precommande->setLocalite($localite);

        $entityManager->persist($precommande);
        $entityManager->flush();

        $message = [
            'id' => $precommande->getIdTransaction(),
            'amount' => $precommande->getMontant(),
            'status' => true,
        ]; //dd($message);

        return $this->json($message);

    }

    /**
     * @Route("/commande/impression/{reference}", name="panier_commande_impression", methods={"GET","POS"})
     */
    public function impression($reference)
    {
        $commande = $this->getDoctrine()->getRepository(Commande::class)->findOneBy(['reference'=>$reference]);

        return $this->render('impression/facture_precommande.html.twig',[
            'commande' => $commande,
        ]);
    }

    /**
     * fonction verification des valeurs
     *
     * @param $donnee
     * @return string
     */
    protected function validForm($donnee)
    {
        $result = htmlspecialchars(stripslashes(trim($donnee)));

        return $result;
    }
}
