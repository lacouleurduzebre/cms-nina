<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\LEI;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LEIType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('flux', TextType::class, array(
                'label' => 'Url du flux LEI'
            ))
            ->add('clef_moda', TextType::class, array(
                'label' => 'Limiter à la clé de modalité :',
                'help' => "Filtrer les résultats pour ne conserver que les fiches répondant à ce critère",
                'required' => false
            ))
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