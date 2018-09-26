<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 07/09/2018
 * Time: 15:53
 */

namespace App\Blocs\Slider;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SliderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nbSlides', ChoiceType::class, array(
            'label' => 'Nombre de slides à afficher simultanément',
            'multiple' => false,
            'expanded' => true,
            'choices' => array(
                '1' => 1,
                '2' => 2,
                '3' => 3
            ),
        ))
            ->add('autoplay', ChoiceType::class, array(
                'label' => 'Lecture automatique',
                'multiple' => false,
                'expanded' => true,
                'choices' => array(
                    'oui' => 1,
                    'non' => 0
                )
            ))
            ->add('fleches', ChoiceType::class, array(
                'label' => 'Flèches de navigation',
                'multiple' => false,
                'expanded' => true,
                'choices' => array(
                    'oui' => 1,
                    'non' => 0
                )
            ))
            ->add('points', ChoiceType::class, array(
                'label' => 'Points de navigation',
                'multiple' => false,
                'expanded' => true,
                'choices' => array(
                    'oui' => 1,
                    'non' => 0
                )
            ))
            ->add('Slide', CollectionType::class, array(
            'entry_type' => SlideType::class,
            'entry_options' => array(
                'label' => false
            ),
            'allow_add' => true,
            'allow_delete' => true,
            'label' => false,
            'label_format' => 'slide',
            'required' => false
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

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        usort($view['Slide']->children, function (FormView $a, FormView $b) {
            $objectA = $a->vars['data'];
            $objectB = $b->vars['data'];

            $posA = $objectA['position'];
            $posB = $objectB['position'];

            if ($posA == $posB) {
                return 0;
            }

            return ($posA < $posB) ? -1 : 1;
        });
    }
}