<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 01/03/2018
 * Time: 11:07
 */

namespace App\Form\Type\Modules;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idModule', HiddenType::class)
            ->add('type', ChoiceType::class, array(
                'empty_data' => null,
                'required' => false,
                'choices' => array(
                    'Texte' => 'Texte',
                    'Image' => 'Image'
                ),
            ))
            ->add('position');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Modules\Module'
        ));
    }

    public function getParent(){
        return FormType::class;
    }
}