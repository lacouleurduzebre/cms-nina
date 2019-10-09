<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\HTML;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HTMLType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextareaType::class, array(
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'notTinymce',
                    'rows' => '5',
                    'placeholder' => 'Collez du code HTML'
                ]
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