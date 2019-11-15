<?php

namespace App\Repository;

use App\Entity\BlocPartage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BlocPartage|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlocPartage|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlocPartage[]    findAll()
 * @method BlocPartage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlocPartageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlocPartage::class);
    }

    // /**
    //  * @return BlocPartage[] Returns an array of BlocPartage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BlocPartage
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
