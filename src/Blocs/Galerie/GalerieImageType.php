<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 07/09/2018
 * Time: 15:53
 */

namespace App\Blocs\Galerie;


use App\Blocs\Image\ImageType;
use App\Form\Type\ImageDefautType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalerieImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('image', ImageDefautType::class, array(
            'label' => false,
        ))
        ->add('lien', TextType::class, array(
            'label' => 'Lien',
            "help" => "Pour les liens externes, ne pas oublier d'ajouter le préfixe http:// ou https://"
        ))
        ->add('blank', ChoiceType::class, array(
            'label' => false,
            'multiple' => true,
            'expanded' => true,
            'choices' => array(
                'Ouvrir dans une nouvelle fenêtre' => 1
            )
        ))
        ->add('position', HiddenType::class, array(
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