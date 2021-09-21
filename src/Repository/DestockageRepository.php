<?php

namespace App\Repository;

use App\Entity\Destockage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Destockage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Destockage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Destockage[]    findAll()
 * @method Destockage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DestockageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Destockage::class);
    }

    public function findListDestockage()
    {
        return $this
            ->createQueryBuilder('d')
            ->addSelect('al')
            ->addSelect('ar')
            ->leftJoin('d.album', 'al')
            ->leftJoin('al.artiste', 'ar')
            ->orderBy('d.date', 'DESC')
            ->getQuery()->getResult()
        ;
    }

    // /**
    //  * @return Destockage[] Returns an array of Destockage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Destockage
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
