<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 29/06/2018
 * Time: 08:58
 */

namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

class RegionType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $regions = Yaml::parseFile('../config/regions.yaml');
        $regions = array_combine(array_values($regions), array_values($regions));

        $resolver->setDefaults(array(
            'choices' => $regions
        ));
    }

    public function getParent(){
        return ChoiceType::class;
    }
}