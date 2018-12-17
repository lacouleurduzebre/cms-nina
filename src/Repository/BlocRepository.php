<?php

namespace App\Repository;

use App\Entity\Bloc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Bloc|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bloc|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bloc[]    findAll()
 * @method Bloc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlocRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Bloc::class);
    }

    public function recherche($motsCles){
        $resultats = [];

        foreach($motsCles as $motCle){
            $qb = $this
                ->createQueryBuilder('b')
                ->where('b.type = "texte"')
                ->where('b.contenu LIKE :motCle')
                ->setParameters(array('motCle' => '%'.$motCle.'%'));

            $blocs = $qb->getQuery()->getResult();
            foreach($blocs as $bloc){
                if($bloc->getPage()){
                    $resultats['page'.$bloc->getPage()->getId()] = $bloc->getPage();
                };
            }
        }

        return $resultats;
    }

//    /**
//     * @return Bloc[] Returns an array of Bloc objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Bloc
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
