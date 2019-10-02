<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 12/07/2018
 * Time: 13:56
 */

namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['type'] != ''){//Ajax
            $infos = Yaml::parseFile('../src/Blocs/'.$options['type'].'/infos.yaml');
            $description = $infos['description'];
            $icone = $infos['icone'];
            $nom = $infos['nom'];
            $label = '<i class="'.$icone.' mrs"></i>'.$nom;
            $builder->add('contenu', 'App\Blocs\\'.$options['type'].'\\'.$options['type'].'Type', array(
                'label' => false,
                'help' => $description,
                'allow_extra_fields' => true,
                'by_reference' => true
            ))
            ->add('type', HiddenType::class, array(
                'data' => $options['type'],
                'label' => $label
            ));

            //SECTION
            if($options['type'] == 'Section'){
                $builder->add('blocsEnfants', CollectionType::class, [
                    'entry_type' => BlocType::class,
                    'allow_add' => true,
                    'allow_extra_fields' => true,
                    'label' => 'Blocs'
                ]);
            }
        }else{//Chargement du formulaire
            $builder->add('type', HiddenType::class)
                ->add('contenu', CollectionType::class, array(
                    'allow_add' => true,
                    'label' => false
                ));
        }

        $builder
            ->add('position', HiddenType::class, array(
            ))
            ->add('class', TextType::class, array(
                'label' => 'Classe'
            ))
            ->add('htmlAvant', TextType::class, array(
                'label' => 'Code HTML à insérer avant le bloc'
            ))
            ->add('htmlApres', TextType::class, array(
                'label' => 'Code HTML à insérer après le bloc'
            ))
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
                ));

            //SECTION
            if($type == 'Section'){
                $form->add('blocsEnfants', CollectionType::class, [
                    'entry_type' => BlocType::class,
                    'allow_add' => true,
                    'allow_extra_fields' => true,
                    'label' => 'Blocs'
                ]);
            }
        }else{
            $form->add('active', null, array(
                'label' => 'Activé',
                'data' => true
            ));
        }
    }
}