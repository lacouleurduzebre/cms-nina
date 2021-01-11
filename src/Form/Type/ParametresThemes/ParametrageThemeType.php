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
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                        'label' => 'Titres H'.$i.' - Police',
                        'attr' => [
                            'data-propriete' => 'font-family'
                        ]
                    ])
                    ->add('couleurH'.$i, ChoixCouleurType::class, [
                        'label' => 'Titres H'.$i.' - Couleur',
                        'attr' => [
                            'data-propriete' => 'color'
                        ]
                    ])
                    ->add('taillePoliceH'.$i, TextType::class, [
                        'label' => 'Titres H'.$i.' - Taille de police',
                        'attr' => [
                            'data-propriete' => 'font-size'
                        ]
                    ]);
                $i++;
            }

            //Textes
            $builder
                ->add('policeTextes', ChoixPoliceType::class, [
                    'label' => 'Textes - Police',
                    'attr' => [
                        'data-propriete' => 'font-family'
                    ]
                ])
                ->add('couleurTextes', ChoixCouleurType::class, [
                    'label' => 'Textes - Couleur',
                    'attr' => [
                        'data-propriete' => 'color'
                    ]
                ])
                ->add('tailleTextes', TextType::class, [
                    'label' => 'Textes - Taille de police',
                    'attr' => [
                        'data-propriete' => 'font-size'
                    ]
                ])

            //Liens
                ->add('couleurLiens', ChoixCouleurType::class, [
                    'label' => 'Liens - Couleur'
                ])

            //Formulaires
                ->add('couleurFormulaires', ChoixCouleurType::class, [
                    'label' => 'Formulaires - Couleur',
                    'attr' => [
                        'data-propriete' => 'color'
                    ]
                ])

            //Étiquettes de catégorie
                ->add('couleurCategories', ChoixCouleurType::class, [
                    'label' => 'Étiquettes de catégorie - Couleur',
                    'attr' => [
                        'data-propriete' => 'background-color'
                    ]
                ])

            //Enregistrement
                ->add('Enregistrer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => [
                'autocomplete' => 'off'
            ]
        ]);
    }

    public function getParent(){
        return FormType::class;
    }
}