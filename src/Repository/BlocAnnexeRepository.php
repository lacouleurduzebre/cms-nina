<?php

namespace App\Repository;

use App\Entity\BlocAnnexe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlocAnnexe|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlocAnnexe|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlocAnnexe[]    findAll()
 * @method BlocAnnexe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlocAnnexeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlocAnnexe::class);
    }

    public function blocsAnnexesAvecLiensMediatheque(){
        $qb = $this
            ->createQueryBuilder('b')
            ->join('b.page', 'p')
            ->where('p.corbeille = 0')
            ->andWhere('b.contenu LIKE :motCle')
            ->setParameters(array('motCle' => '%/uploads/%'));

        return $qb->getQuery()->getResult();
    }
}
