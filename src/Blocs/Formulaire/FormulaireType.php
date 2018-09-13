<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\Formulaire;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormulaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('submit', TextType::class, array(
                'label' => "Texte du bouton d'envoi"
            ))
            ->add('destinataires', CollectionType::class, array(
                'entry_type' => TextType::class,
                'entry_options' => array(
                    'label' => false
                ),
                'label' => 'Destinataires',
                'allow_add' => true,
                'allow_delete' => true
            ))
            ->add('champs', CollectionType::class, array(
                'entry_type' => ChampType::class,
                'entry_options' => array(
                    'label' => false
                ),
                'allow_add' => true,
                'allow_delete' => true,
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