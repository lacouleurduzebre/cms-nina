<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\Bouton;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoutonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lien', TextType::class, array(
                "help" => "Pour les liens externes, ne pas oublier d'ajouter le préfixe http:// ou https://"
            ))
            ->add('texte', TextType::class, array(
                "label" => "Texte du lien",
            ))
            ->add('titre', TextType::class, array(
                "label" => "Titre au survol",
                "required" => false
            ))
            ->add('blank', ChoiceType::class, array(
                "choices" => array(
                    "Ouvrir le lien dans un nouvel onglet" => 1
                ),
                "required" => false,
                "expanded" => true,
                "multiple" => true
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