<?php

namespace App\Repository;

use App\Entity\Encaissement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Encaissement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Encaissement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Encaissement[]    findAll()
 * @method Encaissement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EncaissementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Encaissement::class);
    }
	
	public function findByRecouvrement($recouvrement)
	{
		return $this
			->createQueryBuilder('e')
			->addSelect('r')
			->addSelect('v')
			->addSelect('al')
			->addSelect('ar')
			->leftJoin('e.recouvrement', 'r')
			->leftJoin('e.vente', 'v')
			->leftJoin('v.album', 'al')
			->leftJoin('al.artiste', 'ar')
			->where('e.recouvrement = :recouvrement')
			->setParameter('recouvrement', $recouvrement)
			->getQuery()->getResult()
			;
	}

    // /**
    //  * @return Encaissement[] Returns an array of Encaissement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Encaissement
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
