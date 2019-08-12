<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\Partage;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('facebook', ChoiceType::class, array(
            'label' => false,
            'multiple' => true,
            'expanded' => true,
            'choices' => array(
                'Permettre le partage sur Facebook' => 1
            )
        ))
            ->add('twitter', ChoiceType::class, array(
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => array(
                    'Permettre le partage sur Twitter' => 1
                )
            ))
            ->add('linkedIn', ChoiceType::class, array(
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => array(
                    'Permettre le partage sur LinkedIn' => 1
                )
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
        ));
    }

    public function getParent(){
        return FormType::class;
    }
}