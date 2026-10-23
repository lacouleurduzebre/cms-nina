<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 12/07/2018
 * Time: 13:56
 */

namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaginationType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'choices' => array(
                'Activer la pagination' => 1
            ),
            'expanded' => true,
            'label' => false,
            'multiple' => true
        ));
    }

    public function getParent(): ?string{
        return ChoiceType::class;
    }
}