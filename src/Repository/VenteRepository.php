<?php

namespace App\Repository;

use App\Entity\Vente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Vente|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vente|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vente[]    findAll()
 * @method Vente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vente::class);
    }
	
	public function getListByVendeur($vendeur)
	{
		return $this
			->createQueryBuilder('v')
			->addSelect('a')
			->addSelect('f')
			->addSelect('ve')
			->leftJoin('v.album', 'a')
			->leftJoin('v.facture', 'f')
			->leftJoin('f.vendeur', 've')
			->where('ve.id = :vendeur')
			->andWhere('v.reste > 0')
			->orderBy('a.titre', 'ASC')
			->setParameter('vendeur', $vendeur)
			;
	}
	
	/**
	 * @param $facture
	 * @return int|mixed|string
	 */
    public function findByFacture($facture)
    {
        return $this
            ->createQueryBuilder('v')
            ->addSelect('a')
            ->addSelect('t')
            ->leftJoin('v.album', 'a')
            ->leftJoin('a.artiste', 't')
            ->where('v.facture = :facture')
            ->setParameter('facture', $facture)
            ->getQuery()->getResult()
            ;
    }

    // /**
    //  * @return Vente[] Returns an array of Vente objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Vente
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
