<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 01/03/2018
 * Time: 11:07
 */

namespace App\Form\Type;


use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageAccueilType extends AbstractType
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'required' => false,
            'class' => Page::class,
            'query_builder' => function (EntityRepository $er) use ($langue) {
                return $er->createQueryBuilder('p')
                    ->andWhere('p.langue = :langue')
                    ->setParameters(array('langue' => $langue))
                    ->orderBy('p.titre', 'ASC');
            }
        ));
    }

    public function getParent(){
        return EntityType::class;
    }
}