<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\Formulaire;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChampType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', ChoiceType::class, array(
            'label' => 'Type de champ',
            'choices' => array(
                'Texte' => 'text',
                'Zone de texte' => 'textarea',
                'Sélecteur déroulant' => 'select',
                'Champ à choix (plusieurs réponses possibles)' => 'checkbox',
                'Champ à choix (une réponse)' => 'radio'
            )
        ))
            ->add('requis', ChoiceType::class, array(
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => array(
                    'Champ obligatoire' => 'oui'
                )
            ))
            ->add('label', TextType::class, array(
                'label' => "Libellé"
            ))
            ->add('choix', CollectionType::class, array(
                'entry_type' => TextType::class,
                'entry_options' => array(
                  'label' => false
                ),
                'label' => 'Choix',
                'allow_delete' => true,
                'allow_add' => true,
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'label' => false
        ));
    }

    public function getParent(){
        return FormType::class;
    }
}