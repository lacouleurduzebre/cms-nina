<?php

namespace App\Repository;

use App\Entity\Region;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Region|null find($id, $lockMode = null, $lockVersion = null)
 * @method Region|null findOneBy(array $criteria, array $orderBy = null)
 * @method Region[]    findAll()
 * @method Region[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Region::class);
    }

    /**
     * @return Region[] Returns an array of Region objects
     */
    public function getRegionsAvant($position)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.position < :val')
            ->setParameter('val', $position)
            ->orderBy('r.position', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Region[] Returns an array of Region objects
     */
    public function getRegionsApres($position)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.position > :val')
            ->setParameter('val', $position)
            ->orderBy('r.position', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Region
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
