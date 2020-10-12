<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\LEI\back;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModaliteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('critere', TextType::class, [
                'label' => 'Critère'
            ])
            ->add('modalite', TextType::class, [
                'label' => 'Modalité'
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }

    public function getParent(){
        return FormType::class;
    }
}