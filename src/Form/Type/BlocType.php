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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

class BlocType extends AbstractType
{
    private $optionsAlignementVertical = [
        'label' => 'Alignement vertical du bloc',
        'choices' => [
            '<img src="/assets/img/optionsAffichage/alignVertAuto.svg">' => '',//Automatique
            '<img src="/assets/img/optionsAffichage/alignVertHaut.svg">' => 'mbauto',//En haut
            '<img src="/assets/img/optionsAffichage/alignVertCentre.svg">' => 'mtauto mbauto',//Centré
            '<img src="/assets/img/optionsAffichage/alignVertBas.svg">' => 'mtauto',//En bas
        ],
        'expanded' => true,
        'required' => false,
        'attr' => [
            'class' => 'bloc-optionsAffichage--alignement'
        ]
    ];

    private $optionsAlignementHorizontal = [
        'label' => 'Alignement horizontal du bloc',
        'choices' => [
            '<img src="/assets/img/optionsAffichage/alignHorAuto.svg">' => '',//Automatique
            '<img src="/assets/img/optionsAffichage/alignHorGauche.svg">' => 'mrauto',//À gauche
            '<img src="/assets/img/optionsAffichage/alignHorCentre.svg">' => 'mlauto mrauto',//Centré
            '<img src="/assets/img/optionsAffichage/alignHorDroit.svg">' => 'mlauto',//À droite
        ],
        'expanded' => true,
        'attr' => [
            'class' => 'bloc-optionsAffichage--alignement'
        ]
    ];

    private $optionsAlignementVerticalEnfants = [
        'label' => 'Alignement vertical des blocs enfants',
        'choices' => [
            '<img src="/assets/img/optionsAffichage/alignVertEnfAuto.svg">' => 'stretch',//Sur toute la hauteur
            '<img src="/assets/img/optionsAffichage/alignVertEnfHaut.svg">' => 'flex-start',//En haut
            '<img src="/assets/img/optionsAffichage/alignVertEnfCentre.svg">' => 'center',//Centre
            '<img src="/assets/img/optionsAffichage/alignVertEnfBas.svg">' => 'flex-end',//En bas
        ],
        'expanded' => true,
        'attr' => [
            'class' => 'bloc-optionsAffichage--alignement'
        ]
    ];

    private $optionsAlignementHorizontalEnfants = [
        'label' => 'Alignement horizontal des blocs enfants',
        'choices' => [
            '<img src="/assets/img/optionsAffichage/alignHorEnfGauche.svg">' => 'flex-start',//À gauche
            '<img src="/assets/img/optionsAffichage/alignHorEnfCentre.svg">' => 'center',//Centre
            '<img src="/assets/img/optionsAffichage/alignHorEnfDroit.svg">' => 'flex-end',//À droite
            '<img src="/assets/img/optionsAffichage/alignHorEnfEntre.svg">' => 'space-between',//Répartis avec de l'espace entre les blocs
            '<img src="/assets/img/optionsAffichage/alignHorEnfAutour.svg">' => 'space-around',//Répartis avec de l'espace autour des blocs
        ],
        'expanded' => true,
        'attr' => [
            'class' => 'bloc-optionsAffichage--alignement'
        ]
    ];

    private $optionsGouttieres = [
        'label' => 'Espacement des blocs enfants',
        'choices' => [
            'Aucun' => '',
            'Fin' => 's',
            'Moyen' => 'm',
            'Large' => 'l',
        ],
    ];

    private $optionsPleineLargeur = [
        'label' => "Pleine largeur"
    ];

    private $choixPaddingTout = [
        'Aucune' => 'pan',
        'Fine' => 'pas',
        'Moyenne' => 'pam',
        'Large' => 'pal',
    ];

    private $choixPaddingGauche = [
        'Aucune' => 'pln',
        'Fine' => 'pls',
        'Moyenne' => 'plm',
        'Large' => 'pll',
    ];

    private $choixPaddingDroite = [
        'Aucune' => 'prn',
        'Fine' => 'prs',
        'Moyenne' => 'prm',
        'Large' => 'prl',
    ];

    private $choixPaddingHaut = [
        'Aucune' => 'ptn',
        'Fine' => 'pts',
        'Moyenne' => 'ptm',
        'Large' => 'ptl',
    ];

    private $choixPaddingBas = [
        'Aucune' => 'pbn',
        'Fine' => 'pbs',
        'Moyenne' => 'pbm',
        'Large' => 'pbl',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['type'] != ''){//Ajax
            $infos = Yaml::parseFile('../src/Blocs/'.$options['type'].'/infos.yaml');
            $description = $infos['description'];
            $nom = $infos['nom'];

            //Classes
            $classes = $this->getClasses($options['type']);

            $builder->add('contenu', 'App\Blocs\\'.$options['type'].'\\'.$options['type'].'Type', array(
                'label' => false,
                'help' => $description,
                'allow_extra_fields' => true,
                'by_reference' => true
            ))
            ->add('type', HiddenType::class, array(
                'data' => $options['type'],
                'label' => $nom
            ))
            ->add('classes', ChoiceType::class, array(
                'choices' => $classes,
                'expanded' => false,
                'multiple' => true,
                'attr' => [
                    'class' => 'select-multiple'
                ]
            ));

            //SECTION
            if($options['type'] == 'Section'){
                $builder->add('blocsEnfants', CollectionType::class, [
                    'entry_type' => BlocType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'allow_extra_fields' => true,
                    'label' => false,
                    'by_reference' => true
                ]);
            }
        }else{//Chargement du formulaire
            $builder->add('type', HiddenType::class)
                ->add('classes', HiddenType::class)
                ->add('contenu', CollectionType::class, array(
                    'allow_add' => true,
                    'label' => false
                ));
        }

        $builder
            ->add('position', HiddenType::class)
            ->add('pleineLargeur', null, $this->optionsPleineLargeur)
            ->add('largeur', HiddenType::class)
            ->add('padding', HiddenType::class)
            ->add('paddingTout', ChoiceType::class, [
                'label' => 'Marges intérieures',
                'choices' => $this->choixPaddingTout,
                'mapped' => false
            ])
            ->add('paddingGauche', ChoiceType::class, [
                'label' => 'Marge gauche',
                'choices' => $this->choixPaddingGauche,
                'mapped' => false
            ])
            ->add('paddingDroit', ChoiceType::class, [
                'label' => 'Marge droite',
                'choices' => $this->choixPaddingDroite,
                'mapped' => false
            ])
            ->add('paddingHaut', ChoiceType::class, [
                'label' => 'Marge haute',
                'choices' => $this->choixPaddingHaut,
                'mapped' => false
            ])
            ->add('paddingBas', ChoiceType::class, [
                'label' => 'Marge basse',
                'choices' => $this->choixPaddingBas,
                'mapped' => false
            ])
            ->add('alignementVertical', ChoiceType::class, $this->optionsAlignementVertical)
            ->add('alignementHorizontal', ChoiceType::class, $this->optionsAlignementHorizontal)
            ->add('alignementVerticalEnfants', ChoiceType::class, $this->optionsAlignementVerticalEnfants)
            ->add('alignementHorizontalEnfants', ChoiceType::class, $this->optionsAlignementHorizontalEnfants)
            ->add('gouttieres', ChoiceType::class, $this->optionsGouttieres)
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $this->ajoutChamps($event);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $this->ajoutChamps($event);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'label' => false,
            'data_class' => 'App\Entity\Bloc',
            'type' => '',
            'required' => false,
            "allow_extra_fields" => true,
            'allow_add' => true
        ));
    }

    public function getParent(){
        return FormType::class;
    }

    public function ajoutChamps(FormEvent $event){
        $bloc = $event->getData();
        $form = $event->getForm();

        if ($bloc){//Bloc déjà existant
            $type = is_array($bloc) ? $bloc['type'] : $bloc->getType();
            $infos = Yaml::parseFile('../src/Blocs/'.$type.'/infos.yaml');
            $nom = $infos['nom'];

            //Classes
            $classes = $this->getClasses($type);

            $form->add('contenu', 'App\Blocs\\'.$type.'\\'.$type.'Type', array(
                'label' => false,
                'help' => $infos['description'],
                'allow_extra_fields' => true,
            ))
                ->add('type', HiddenType::class, array(
                    'label' => $nom
                ))
                ->add('active', HiddenType::class, array(
                    'label' => 'Activé'
                ))
                ->add('classes', ChoiceType::class, array(
                    'choices' => $classes,
                    'expanded' => false,
                    'multiple' => true,
                    'attr' => [
                        'class' => 'select-multiple'
                    ]
                ))
                ->add('pleineLargeur', null, $this->optionsPleineLargeur)
                ->add('largeur', HiddenType::class)
                ->add('padding', HiddenType::class)
                ->add('paddingTout', ChoiceType::class, [
                    'label' => 'Marges intérieures',
                    'choices' => $this->choixPaddingTout,
                    'mapped' => false
                ])
                ->add('paddingGauche', ChoiceType::class, [
                    'label' => 'Marge gauche',
                    'choices' => $this->choixPaddingGauche,
                    'mapped' => false
                ])
                ->add('paddingDroit', ChoiceType::class, [
                    'label' => 'Marge droite',
                    'choices' => $this->choixPaddingDroite,
                    'mapped' => false
                ])
                ->add('paddingHaut', ChoiceType::class, [
                    'label' => 'Marge haute',
                    'choices' => $this->choixPaddingHaut,
                    'mapped' => false
                ])
                ->add('paddingBas', ChoiceType::class, [
                    'label' => 'Marge basse',
                    'choices' => $this->choixPaddingBas,
                    'mapped' => false
                ])
                ->add('alignementVertical', ChoiceType::class, $this->optionsAlignementVertical)
                ->add('alignementHorizontal', ChoiceType::class, $this->optionsAlignementHorizontal)
                ->add('alignementVerticalEnfants', ChoiceType::class, $this->optionsAlignementVerticalEnfants)
                ->add('alignementHorizontalEnfants', ChoiceType::class, $this->optionsAlignementHorizontalEnfants)
                ->add('gouttieres', ChoiceType::class, $this->optionsGouttieres)
            ;

            //SECTION
            if($type == 'Section'){
                $form->add('blocsEnfants', CollectionType::class, [
                    'entry_type' => BlocType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'allow_extra_fields' => true,
                    'label' => false,
                    'by_reference' => false
                ]);
            }
        }else{
            $form->add('active', HiddenType::class, array(
                'label' => 'Activé',
                'data' => true
            ));
        }
    }

    public function getClasses($type){
        $classes = [];

        //Générales
        $infosBloc = Yaml::parseFile('../src/Blocs/'.$type.'/infos.yaml');
        if(isset($infosBloc['classes'])){
            $classes = array_merge($classes, $infosBloc['classes']);
        }

        //Spécifiques au thème
        $config = Yaml::parseFile('../config/services.yaml');
        $theme = $config['parameters']['theme'];

        if(file_exists('../themes/'.$theme.'/config.yaml')){
            $infosBlocTheme = Yaml::parseFile('../themes/'.$theme.'/config.yaml');
            if(isset($infosBlocTheme['blocs'][$type]['classes'])){
                $classes = array_merge($classes, $infosBlocTheme['blocs'][$type]['classes']);
            }
        }

        return $classes;
    }
}