<?php

namespace App\Repository;

use App\Entity\Modules\ModuleTexte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ModuleTexte|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModuleTexte|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModuleTexte[]    findAll()
 * @method ModuleTexte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleTexteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ModuleTexte::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('m')
            ->where('m.something = :value')->setParameter('value', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
