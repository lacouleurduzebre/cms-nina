<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\LogoSite;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogoSiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('logo', ChoiceType::class, array(
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => array(
                    'Afficher le logo du site' => 1
                )
            ))
            ->add('nom', ChoiceType::class, array(
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => array(
                    'Afficher le nom du site' => 1
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