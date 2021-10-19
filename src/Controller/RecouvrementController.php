<?php

namespace App\Controller;

use App\Entity\Recouvrement;
use App\Utilities\GestionFacture;
use App\Utilities\GestionRecouvrement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/recouvrement")
 */
class RecouvrementController extends AbstractController
{
	private $_facture;
	private $_recouvrement;
	
	public function __construct(GestionFacture $_facture, GestionRecouvrement $_recouvrement)
	{
		$this->_facture = $_facture;
		$this->_recouvrement = $_recouvrement;
	}
	
    /**
     * @Route("/", name="recouvrement_ajax", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
	    //Initialisation
	    $encoders = [new XmlEncoder(), new JsonEncoder()];
	    $normalizers = [new ObjectNormalizer()];
	    $serializer = new Serializer($normalizers, $encoders);
	
	    $reference = $request->get('reference');
		
		if ($reference){
			$recouvrement = $this->getDoctrine()->getRepository(Recouvrement::class)->findOneBy(['reference'=>$reference]);
			$recouvrement->setFlag(true);
			$this->getDoctrine()->getManager()->flush();
			
			$message = [
				'reference' => $recouvrement->getReference(),
				'status' => true,
			];
		}else{
			$message = [
				'reference' => '',
				'text' => 'error'
			];
		}
        return $this->json($message);
    }
	
	/**
	 * @Route("/{reference}", name="recouvrement_impression_facture", methods={"GET","POST"})
	 */
	public function impression(Recouvrement $recouvrement)
	{
		$lists = $this->_recouvrement->getEncaissementByRecouvrement($recouvrement->getId());
		//dd($lists);
		return $this->render('facturation/recouvrement.html.twig',[
			'encaissements' => $lists,
			'recouvrement' => $recouvrement
		]);
	}
}
