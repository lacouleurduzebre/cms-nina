<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\Videos;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('limite', NumberType::class, array(
                'label' => 'Nombre limite de résultats',
                'help' => "Si aucune limite n'est précisée, tous les résultats seront affichés",
                'required' => false
            ))
            ->add('pagination', ChoiceType::class, array(
                'choices' => array(
                    'Activer la pagination' => 1
                ),
                'expanded' => true,
                'label' => false,
                'multiple' => true
            ))
            ->add('resultatsParPage', NumberType::class, array(
                'label' => 'Nombre de résultats par page'
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