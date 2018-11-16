<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\ReseauxSociaux;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReseauxSociauxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('facebook', ChoiceType::class, array(
            'label' => false,
            'multiple' => true,
            'expanded' => true,
            'choices' => array(
                'Facebook' => 1
            )
        ))
            ->add('facebookUrl', TextType::class, array(
                'label' => 'Lien :'
            ))
            ->add('twitter', ChoiceType::class, array(
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => array(
                    'Twitter' => 1
                )
            ))
            ->add('twitterUrl', TextType::class, array(
                'label' => 'Lien :'
            ))
            ->add('instagram', ChoiceType::class, array(
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => array(
                    'Instagram' => 1
                )
            ))
            ->add('instagramUrl', TextType::class, array(
                'label' => 'Lien :'
            ))
            ->add('youtube', ChoiceType::class, array(
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => array(
                    'Youtube' => 1
                )
            ))
            ->add('youtubeUrl', TextType::class, array(
                'label' => 'Lien :'
            ))
            ->add('linkedIn', ChoiceType::class, array(
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => array(
                    'LinkedIn' => 1
                )
            ))
            ->add('linkedInUrl', TextType::class, array(
                'label' => 'Lien :'
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