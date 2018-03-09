<?php

namespace App\Repository;

use App\Entity\Modules\ModuleImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ModuleImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModuleImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModuleImage[]    findAll()
 * @method ModuleImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleImageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ModuleImage::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('i')
            ->where('i.something = :value')->setParameter('value', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
