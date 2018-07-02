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

class ThemeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $themes = scandir('themes');
        unset($themes[0]);
        unset($themes[1]);
        $themes = array_combine(array_values($themes), array_values($themes));

        $resolver->setDefaults(array(
            'choices' => $themes
        ));
    }

    public function getParent(){
        return ChoiceType::class;
    }
}