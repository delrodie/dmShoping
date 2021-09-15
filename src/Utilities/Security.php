<?php

namespace App\Utilities;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class Security
{
    private $entityManager;
    private $passwordEncoder;
    private $security;
    private $userRepository;
    private $session;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder, \Symfony\Component\Security\Core\Security $security, UserRepository $userRepository, SessionInterface $session)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->session = $session;
    }

    /**
     * Initialisation des utilisateurs par la creation du compte du super admin
     *
     * @return bool
     */
    public function initalisationUser()
    {
        $user = new User();
        $user->setUsername('delrodie');
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'dreammaker2021'));
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $user->setEmail('delrodieamoikon@gmail.com');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Mise a jour de la table User
     *
     * @return bool
     */
    public function connexion()
    {
        //$user = $this->security->getUser();
        $user = $this->userRepository->findOneBy(['username'=>$this->security->getUser()->getUsername()]); //dd($user);

        $nombre_connexion = $user->getConnexion();
        //$date = new \DateTime();
        $user->setConnexion($nombre_connexion+1);
        $user->setLastconnectedAt(new \DateTime());

        $this->entityManager->flush();

        return true;
    }

    public function session()
    {
        $tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvxyz1234567890";
        $tokenSession = null; $res=null;
        for ($i=0; $i<25; $i++){
            $res = $tab[rand(0,60)];
            $tokenSession = $tokenSession.''.$res;
        }

        return $this->session->set('updatePassword', $tokenSession);
    }

}