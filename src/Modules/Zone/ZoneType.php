<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Modules\Zone;


use App\Entity\Categorie;
use App\Entity\Zone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ZoneType extends AbstractType
{
    public function __construct(EntityManagerInterface $em)
    {
     $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $repoZone = $this->em->getRepository(Zone::class);
        $objetsZones = $repoZone->findAll();
        $zones = [];
        foreach($objetsZones as $objetZone){
            $zones[$objetZone->getNom()] = $objetZone->getId();
        }

        $builder
            ->add('zone', ChoiceType::class, array(
                'choices' => $zones
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }

    public function getParent(){
        return FormType::class;
    }
}