<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 01/03/2018
 * Time: 11:07
 */

namespace App\Form\Type;


use Symfony\Component\OptionsResolver\OptionsResolver;

class SEOTypeCategorieType extends SEOType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\SEOTypeCategorie'
        ));
    }
}