<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2021-01-07
 * Time: 13:36
 */

namespace App\Form\Type\ParametresThemes;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ParametrageThemeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //Couleurs
            ->add('couleur1', ColorType::class, [
                'label' => 'Couleur 1'
            ])
            ->add('couleur2', ColorType::class, [
                'label' => 'Couleur 2'
            ])
            ->add('couleur3', ColorType::class, [
                'label' => 'Couleur 3'
            ])

            //Polices à importer
            ->add('polices', PolicesType::class, [
                'label' => 'Polices'
            ]);

            //Titres
            $i = 1;
            while($i <= 4){
                $builder
                    ->add('policeH'.$i, ChoixPoliceType::class, [
                        'label' => 'Titres H'.$i.' - Police'
                    ])
                    ->add('couleurH'.$i, ChoixCouleurType::class, [
                        'label' => 'Titres H'.$i.' - Couleur'
                    ])
                    ->add('taillePoliceH'.$i, TextType::class, [
                        'label' => 'Titres H'.$i.' - Taille de police'
                    ]);
                $i++;
            }

            //Textes
            $builder
                ->add('policeTextes', ChoixPoliceType::class, [
                    'label' => 'Textes - Police'
                ])
                ->add('couleurTextes', ChoixCouleurType::class, [
                    'label' => 'Textes - Couleur'
                ])
                ->add('tailleTextes', TextType::class, [
                    'label' => 'Textes - Taille de police'
                ])

            //Liens
                ->add('couleurLiens', ChoixCouleurType::class, [
                    'label' => 'Liens - Couleur'
                ])

            //Formulaires
                ->add('couleurFormulaires', ChoixCouleurType::class, [
                    'label' => 'Formulaires - Couleur'
                ])

            //Étiquettes de catégorie
                ->add('couleurCategories', ChoixCouleurType::class, [
                    'label' => 'Étiquettes de catégorie - Couleur'
                ])

            //Enregistrement
                ->add('Enregistrer', SubmitType::class);
    }

    public function getParent(){
        return FormType::class;
    }
}