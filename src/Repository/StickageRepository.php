<?php

namespace App\Repository;

use App\Entity\Stickage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Stickage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stickage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stickage[]    findAll()
 * @method Stickage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StickageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stickage::class);
    }

    public function findList()
    {
        return $this
            ->createQueryBuilder('s')
            ->addSelect('al')
            ->addSelect('ar')
            ->leftJoin('s.album', 'al')
            ->leftJoin('al.artiste', 'ar')
            ->orderBy('s.date', 'DESC')
            ->getQuery()->getResult()
            ;
    }

    // /**
    //  * @return Stickage[] Returns an array of Stickage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Stickage
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
