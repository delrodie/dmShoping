<?php

namespace App\Repository;

use App\Entity\Recouvrement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Recouvrement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recouvrement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recouvrement[]    findAll()
 * @method Recouvrement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecouvrementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recouvrement::class);
    }

    public function findListe()
    {
        return $this
            ->createQueryBuilder('r')
            ->addSelect('v')
            ->leftJoin('r.vendeur', 'v')
            ->orderBy('r.date', 'DESC')
            ->getQuery()->getResult()
            ;
    }
	
	public function getTotalMois($periode)
	{
		return $this->createQueryBuilder('r')
			->select('SUM(r.montant)')
			->where('r.date BETWEEN :debut AND :fin')
			->setParameters([
				'debut' => $periode['debut'],
				'fin' => $periode['fin']
			])
			->getQuery()->getSingleScalarResult()
			;
	}

    // /**
    //  * @return Recouvrement[] Returns an array of Recouvrement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Recouvrement
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
