<?php

namespace App\Repository;

use App\Entity\Pressage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pressage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pressage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pressage[]    findAll()
 * @method Pressage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PressageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pressage::class);
    }

    public function findList()
    {
        return $this
            ->createQueryBuilder('p')
            ->addSelect('al')
            ->addSelect('ar')
            ->leftJoin('p.album', 'al')
            ->leftJoin('al.artiste', 'ar')
            ->orderBy('p.date', 'DESC')
            ->getQuery()->getResult()
            ;
    }

    // /**
    //  * @return Pressage[] Returns an array of Pressage objects
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
    public function findOneBySomeField($value): ?Pressage
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
