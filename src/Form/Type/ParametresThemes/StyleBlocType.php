<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2021-01-11
 * Time: 14:12
 */

namespace App\Form\Type\ParametresThemes;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StyleBlocType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('couleurFond', ColorType::class, [
                'label' => 'Couleur de fond'
            ])
            ->add('opaciteFond', PercentType::class, [
                'label' => 'Opacité du fond'
            ])
            ->add('couleur', ColorType::class, [
                'label' => 'Couleur du texte',
                'attr' => [
                    'data-propriete' => 'color'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false
        ]);
    }

    public function getParent(){
        return FormType::class;
    }
}