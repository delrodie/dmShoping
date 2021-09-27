<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Utilities\GestionFacture;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/facturation")
 */
class FacturationController extends AbstractController
{
    private $gestionFacture;

    public function __construct(GestionFacture $gestionFacture)
    {
        $this->gestionFacture = $gestionFacture;
    }

    /**
     * @Route("/", name="facturation_ajax", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {

        //Initialisation
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $reference = $request->get('reference');

        if ($reference){
            $facture = $this->getDoctrine()->getRepository(Facture::class)->findOneBy(['reference' => $reference]);
            $facture->setFlag(true);
            $this->getDoctrine()->getManager()->flush();

            // Mise a jour des finances du vendeur
            $this->gestionFacture->vendeurFinance($facture->getVendeur(), $facture->getTtc(), true);

            $message = [
                'reference' => $facture->getReference(),
                'status' => true,
            ];

            return $this->json($message);
        }else{
            $message = [
                'id' => '',
                'text' => 'erreur',
            ];
            return $this->json($message);
        }

    }

    /**
     * @Route("/{reference}", name="facturation_impression_bl", methods={"GET"})
     */
    public function impression(Facture $facture)
    {
        //dd();
        return $this->render('facturation/index.html.twig',[
            'facture'=>$facture,
            'ventes' => $this->gestionFacture->listVente($facture->getId())
        ]);
    }
}
