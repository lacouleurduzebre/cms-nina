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

class BlocAnnexeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['type'] != ''){//Ajax
            $infos = Yaml::parseFile('../src/BlocsAnnexes/'.$options['type'].'/infos.yaml');
            $description = $infos['description'];
            $nom = $infos['nom'];
            $builder->add('contenu', 'App\BlocsAnnexes\\'.$options['type'].'\\'.$options['type'].'Type', array(
                'label' => false,
                'help' => $description,
                'allow_extra_fields' => true,
                'by_reference' => true
            ))
            ->add('type', HiddenType::class, array(
                'data' => $options['type'],
                'label' => $nom
            ));
        }else{//Chargement du formulaire
            $builder->add('type', HiddenType::class)
                ->add('contenu', CollectionType::class, array(
                    'allow_add' => true,
                    'label' => false
                ));
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $bloc = $event->getData();
            $form = $event->getForm();

            if ($bloc){//Bloc déjà existant
                $type = $bloc->getType();
                $infos = Yaml::parseFile('../src/BlocsAnnexes/'.$type.'/infos.yaml');
                $form->add('contenu', 'App\BlocsAnnexes\\'.$type.'\\'.$type.'Type', array(
                    'label' => false,
                    'help' => $infos['description'],
                    'allow_extra_fields' => true,
                    'attr' => array(
                        'class' => 'hide'
                    )
                ))
                    ->add('type', HiddenType::class, array(
                        'label' => $infos['nom']
                    ));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'label' => false,
            'data_class' => 'App\Entity\BlocAnnexe',
            'type' => '',
            'required' => false,
            "allow_extra_fields" => true,
            'allow_add' => true
        ));
    }

    public function getParent(){
        return FormType::class;
    }
}