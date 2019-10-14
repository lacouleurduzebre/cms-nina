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
    private $optionsLargeur = [
        'label' => 'Largeur par rapport au conteneur',
        'choices' => [
            '100%' => '100',
            '80%' => '80',
            '60%' => '60',
            '40%' => '40',
            '20%' => '20',
        ],
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['type'] != ''){//Ajax
            $infos = Yaml::parseFile('../src/Blocs/'.$options['type'].'/infos.yaml');
            $description = $infos['description'];
            $icone = $infos['icone'];
            $nom = $infos['nom'];
            $label = '<i class="'.$icone.' mrs"></i>'.$nom;

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
                'label' => $label
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
            ->add('largeur', ChoiceType::class, $this->optionsLargeur)
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
            $icone = $infos['icone'];
            $nom = $infos['nom'];
            $label = '<i class="'.$icone.' mrs"></i>'.$nom;

            //Classes
            $classes = $this->getClasses($type);

            $form->add('contenu', 'App\Blocs\\'.$type.'\\'.$type.'Type', array(
                'label' => false,
                'help' => $infos['description'],
                'allow_extra_fields' => true,
            ))
                ->add('type', HiddenType::class, array(
                    'label' => $label
                ))
                ->add('active', null, array(
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
                ->add('largeur', ChoiceType::class, $this->optionsLargeur);

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
            $form->add('active', null, array(
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