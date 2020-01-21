<?php

namespace App\Repository;

use App\Entity\Bloc;
use App\Service\Page;
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
        $typesBlocs = ['titre', 'texte', 'paragraphe'];
        $resultats = [];
        $blocs = [];

        foreach($typesBlocs as $typeBloc){
            foreach($motsCles as $motCle){
                $qb = $this
                    ->createQueryBuilder('b')
                    ->where('b.type = :type')
                    ->andWhere('b.contenu LIKE :motCle')
                    ->setParameters(array('motCle' => '%'.$motCle.'%', 'type' => $typeBloc));

                $blocsTrouves = $qb->getQuery()->getResult();
                $blocs = array_merge($blocs, $blocsTrouves);
            }
        }

        $timestamp = new \DateTime();
        $timestamp = $timestamp->getTimestamp();

        foreach($blocs as $bloc){
            if(!$bloc->getGroupeBlocs()){
                while($bloc->getBlocParent()){
                    $bloc = $bloc->getBlocParent();
                }

                $page = $bloc->getPage();

                if(!Page::isPublie($page)) {
                    continue;
                }

                $resultats['page'.$page->getId()] = $page;
            }
        }

        return $resultats;
    }

    public function videosPagesPubliees($langue){
        $timestamp = new \DateTime();
        $date = $timestamp->format('Y-m-d H:i:s');

        $qb = $this
            ->createQueryBuilder('b')
            ->join('b.page', 'p')
            ->andWhere('b.type = :type')
            ->andWhere('p.corbeille = 0')
            ->andWhere('p.active = 1')
            ->andWhere('p.langue = :langue')
            ->andWhere('p.datePublication < :date')
            ->andWhere('p.dateDepublication > :date OR p.dateDepublication IS NULL')
            ->setParameters(array('langue' => $langue, 'date' => $date, 'type' => 'Video'))
            ->orderBy('p.datePublication', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function blocsAvecImagesMediatheque(){
        $qb = $this
            ->createQueryBuilder('b')
            ->join('b.page', 'p')
            ->where('p.corbeille = 0')
            ->andWhere('b.contenu LIKE :motCle')
            ->setParameters(array('motCle' => '%/uploads/%'));

        return $qb->getQuery()->getResult();
    }
}
