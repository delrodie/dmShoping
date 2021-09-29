<?php

namespace App\Repository;

use App\Entity\Album;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Album|null find($id, $lockMode = null, $lockVersion = null)
 * @method Album|null findOneBy(array $criteria, array $orderBy = null)
 * @method Album[]    findAll()
 * @method Album[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Album::class);
    }

    public function liste()
    {
        return $this->createQueryBuilder('a')->orderBy('a.titre', 'ASC');
    }

    /**
     * @return int|mixed|string
     */
    public function findList()
    {
        return $this
            ->createQueryBuilder('al')
            ->addSelect('ar')
            ->addSelect('g')
            ->leftJoin('al.artiste', 'ar')
            ->leftJoin('al.genre', 'g')
            ->orderBy('al.id', 'DESC')
            ->getQuery()->getResult()
            ;
    }

    public function findBySlug($slug)
    {
        return $this
            ->createQueryBuilder('al')
            ->addSelect('ar')
            ->addSelect('g')
            ->leftJoin('al.artiste', 'ar')
            ->leftJoin('al.genre', 'g')
            ->where('al.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('al.id', 'DESC')
            ->getQuery()->getOneOrNullResult()
            ;
    }

    // /**
    //  * @return Album[] Returns an array of Album objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Album
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
