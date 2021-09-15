<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Utilities\GestionLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/user")
 */
class AdminUserController extends AbstractController
{
    private $passwordHash;
    private $gestionLog;

    public function __construct(UserPasswordHasherInterface $passwordHash, GestionLog $gestionLog)
    {
        $this->passwordHash = $passwordHash;
        $this->gestionLog = $gestionLog;
    }

    /**
     * @Route("/", name="admin_user_index", methods={"GET","POST"})
     */
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Encodage du mot de passe
            $password = $this->passwordHash->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            $entityManager->persist($user);
            $entityManager->flush();

            // Ajout du log
            $this->gestionLog->addLogger($this->log($user->getUserIdentifier())['ajout']);

            $this->addFlash('success', "L'utilisateur ".$user->getUserIdentifier()." a bien été enregistré");

            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        // Ajout du log
        $this->gestionLog->addLogger($this->log()['affichage']);

        return $this->renderForm('admin_user/index.html.twig', [
            'users' => $userRepository->findListWithoutDelrodie(),
            'user' => $user,
            'form' => $form
        ]);
    }

    /**
     * @Route("/new", name="admin_user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('admin_user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Encodage du mot de passe
            if ($user->getPassword()){
                $password = $this->passwordHash->hashPassword($user, $user->getPassword());
                $user->setPassword($password);
            }

            $this->getDoctrine()->getManager()->flush();

            // Ajout du log
            $this->gestionLog->addLogger($this->log($user->getUserIdentifier())['modif']);

            $this->addFlash('success', "L'utilisateur ".$user->getUserIdentifier()." a bien été modifié");

            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_user/edit.html.twig', [
            'users' => $userRepository->findListWithoutDelrodie(),
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $user_deleted = $user->getUserIdentifier();
            $entityManager->remove($user);
            $entityManager->flush();

            // Ajout du log
            $this->gestionLog->addLogger($this->log($user_deleted)['sup']);
        }

        return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
    }


    protected function log($action=null)
    {
        $user = $this->getUser()->getUserIdentifier();
        $date = date('Y-m-d H:i:s');
        $val = [
            'affichage' => $user." a visualisé la liste des utilisateurs le ".$date." via l'IP ",
            'ajout' => $user." a enregistré l'utilisateur ".$action." le ".$date." via l'IP ",
            'modif' => $user." a modifié l'utilisateur ".$action." le ".$date." via l'IP ",
            'sup' => $user." a supprimé l'utilisateur ".$action." le ".$date." via l'IP ",
        ];

        return $val;
    }
}
