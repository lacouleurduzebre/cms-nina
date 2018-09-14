<?php

namespace App\Repository;

use App\Entity\GroupeBlocs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GroupeBlocs|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupeBlocs|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupeBlocs[]    findAll()
 * @method GroupeBlocs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupeBlocsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GroupeBlocs::class);
    }

//    /**
//     * @return GroupeBlocs[] Returns an array of GroupeBlocs objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GroupeBlocs
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
