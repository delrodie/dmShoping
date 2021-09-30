<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Precommande;
use CinetPay\CinetPay;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/cinetpay")
 */
class CinetpayController extends AbstractController
{
    /**
     * @Route("/notify", name="cinetpay_notify", methods={"GET","POST"})
     */
    public function notify(Request $request): Response
    {$encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $em = $this->getDoctrine()->getManager();

        $cpmTransId = $request->get('cpm_trans_id');

        if (isset($cpmTransId)){
            try{
                // Initialisation de CinetPay et Identification du paiement
                $id_transaction = $cpmTransId;
                $apiKey = '738218945615320aa597ff3.35893469';
                $site_id = 444572;
                $plateform = "PROD"; // Valorisé à PROD si vous êtes en production
                $CinetPay = new CinetPay($site_id, $apiKey, $plateform);
                // Reprise exacte des bonnes données chez CinetPay
                $CinetPay->setTransId($id_transaction)->getPayStatus();
                $cpm_site_id = $CinetPay->_cpm_site_id;
                $signature = $CinetPay->_signature;
                $cpm_amount = $CinetPay->_cpm_amount;
                $cpm_trans_id = $CinetPay->_cpm_trans_id;
                $cpm_custom = $CinetPay->_cpm_custom;
                $cpm_currency = $CinetPay->_cpm_currency;
                $cpm_payid = $CinetPay->_cpm_payid;
                $cpm_payment_date = $CinetPay->_cpm_payment_date;
                $cpm_payment_time = $CinetPay->_cpm_payment_time;
                $cpm_error_message = $CinetPay->_cpm_error_message;
                $payment_method = $CinetPay->_payment_method;
                $cpm_phone_prefixe = $CinetPay->_cpm_phone_prefixe;
                $cel_phone_num = $CinetPay->_cel_phone_num;
                $cpm_ipn_ack = $CinetPay->_cpm_ipn_ack;
                $created_at = $CinetPay->_created_at;
                $updated_at = $CinetPay->_updated_at;
                $cpm_result = $CinetPay->_cpm_result;
                $cpm_trans_status = $CinetPay->_cpm_trans_status;
                $cpm_designation = $CinetPay->_cpm_designation;
                $buyer_name = $CinetPay->_buyer_name;

                // Vérification de l'effectivité de l'op&ration de Cinetpay
                if ($cpm_result === '00'){
                    // Verification de l'operation dans la table Precommande
                    // Si la precommande existe et validée alors renvoyer message sinon enregistrer
                    $precommande = $this->getDoctrine()->getRepository(Precommande::class)->findOneBy(['idTransaction'=>$cpmTransId]);
                    if($precommande->getStatusTransaction() === 'VALID'){
                        $message = [
                            'id' => $id_transaction,
                        ];

                        return $this->json($message);
                    }else{
                        $commande = new Commande();
                        $commande->setReference($precommande->getReference());
                        $commande->setNom($precommande->getNom());
                        $commande->setTel($precommande->getTel());
                        $commande->setAdresse($precommande->getAdresse());
                        $commande->setEmail($precommande->getEmail());
                        $commande->setQuantite($precommande->getQuantite());
                        $commande->setMontant($precommande->getMontant());
                        $commande->setAlbum($precommande->getAlbum());
                        $commande->setLocalite($precommande->getLocalite());

                        $commande->setIdTransaction($id_transaction);
                        $commande->setStatusTransaction($cpm_trans_status);
                        $commande->setTelTransaction($cel_phone_num);
                        $commande->setDateTransaction($cpm_payment_date);
                        $commande->setTimeTransaction($cpm_payment_time);

                        $em->persist($commande);
                        $em->flush();

                        // Mise a jour de la table precommande
                        $precommande->setStatusTransaction('VALID');
                        $em->flush();

                        $message = [
                            'id' => $id_transaction,
                            'reference' => $commande->getReference(),
                        ];

                        return $this->json($message);
                    }
                }else{
                    die('error');
                }
            } catch (Exception $e){
                echo "Erreur : " .$e->getMessage();
            }
        }

        return $this->render('cinetpay/index.html.twig', [
            'controller_name' => 'CinetpayController',
        ]);
    }
}
