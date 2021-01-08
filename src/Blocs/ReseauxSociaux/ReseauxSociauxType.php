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
        $builder
            ->add('typeRS', ChoiceType::class, [
                'label' => false,
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Afficher des liens vers les réseaux sociaux' => 'liens',
                    'Permettre le partage de la page sur les réseaux sociaux' => 'partage'
                ]
            ])
            ->add('facebook', ChoiceType::class, array(
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => array(
                    'Facebook' => 1
                )
            ))
            ->add('facebookUrl', TextType::class, array(
                'label' => 'Lien Facebook :'
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
                'label' => 'Lien Twitter :'
            ))
            ->add('instagramUrl', TextType::class, array(
                'label' => 'Lien Instagram :'
            ))
            ->add('youtubeUrl', TextType::class, array(
                'label' => 'Lien YouTube :'
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
                'label' => 'Lien LinkedIn :'
            ))
            ->add('emailUrl', TextType::class, array(
                'label' => 'Adresse mail :'
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