<?php

namespace App\Repository;

use App\Entity\BlocAnnexe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BlocAnnexe|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlocAnnexe|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlocAnnexe[]    findAll()
 * @method BlocAnnexe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlocAnnexeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BlocAnnexe::class);
    }

//    /**
//     * @return BlocAnnexe[] Returns an array of BlocAnnexe objects
//     */
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
    public function findOneBySomeField($value): ?BlocAnnexe
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
