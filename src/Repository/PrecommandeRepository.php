<?php

namespace App\Repository;

use App\Entity\Precommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Precommande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Precommande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Precommande[]    findAll()
 * @method Precommande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrecommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Precommande::class);
    }

    public function findByStatut($statut)
    {
        return $this
            ->createQueryBuilder('p')
            ->addSelect('a')
            ->addSelect('ar')
            ->addSelect('l')
            ->leftJoin('p.album', 'a')
            ->leftJoin('p.localite', 'l')
            ->leftJoin('a.artiste', 'ar')
            ->where('p.statusTransaction = :status')
            ->setParameter('status', $statut)
            ->orderBy('p.id', 'DESC')
            ->getQuery()->getResult();

    }

    // /**
    //  * @return Precommande[] Returns an array of Precommande objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Precommande
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
