<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/change-password")
 */
class ChangePasswordController extends AbstractController
{
    /**
     * @Route("/", name="app_change_password", methods={"GET","POST"})
     */
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher, SessionInterface $session)
    {
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);
        # tetste

        if ($form->isSubmitted() && $form->isValid()){
            $user = $this->getUser();

            $request_token = $request->get('_token');
            $session_token = $session->get('updatePassword');

            if ($request_token !== $session_token){
                $this->addFlash('danger', "Veuillez vous reconnecter pour modifier votre mot de passe");
                return $this->redirectToRoute('app_logout');
            }

            $encodePassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodePassword);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Mot de passe changé avec succès");

            return $this->redirectToRoute('app_logout');
        }
        return $this->render('security/change_password.html.twig', [
            'resetForm' => $form->createView(),
            'token' => $session->get('updatePassword')
        ]);
    }

}