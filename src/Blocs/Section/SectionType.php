<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 07/09/2018
 * Time: 15:53
 */

namespace App\Blocs\Section;


use App\Form\Type\BlocType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('colonnes', ChoiceType::class, array(
            'label' => false,
            'multiple' => false,
            'expanded' => true,
            'choices' => array(
                '1 colonne' => '1',
                '2 colonnes : ½ ½' => '1/2',
                '2 colonnes : ⅓ ⅔' => '1/3',
                '2 colonnes : ⅔ ⅓' => '2/3',
                '3 colonnes' => '3',
                '4 colonnes' => '4'
            ),
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