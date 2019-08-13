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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SliderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $slider = $event->getData();
            $form = $event->getForm();

            $optionsNbSlides = [
                'label' => 'Nombre de slides à afficher simultanément',
                'multiple' => false,
                'expanded' => true,
                'choices' => array(
                    '1' => 1,
                    '2' => 2,
                    '3' => 3
                )
            ];

            $optionsAutoplay = [
                'label' => 'Lecture automatique',
                'multiple' => false,
                'expanded' => true,
                'choices' => array(
                    'oui' => 1,
                    'non' => 0
                )
            ];

            $optionsFleches = [
                'label' => 'Flèches de navigation',
                'multiple' => false,
                'expanded' => true,
                'choices' => array(
                    'oui' => 1,
                    'non' => 0
                )
            ];

            $optionsPoints = [
                'label' => 'Points de navigation',
                'multiple' => false,
                'expanded' => true,
                'choices' => array(
                    'oui' => 1,
                    'non' => 0
                )
            ];

            if(!$slider){//Nouveau slider
                $optionsNbSlides['data'] = 1;
                $optionsAutoplay['data'] = 1;
                $optionsFleches['data'] = 1;
                $optionsPoints['data'] = 1;
            }

            $form->add('nbSlides', ChoiceType::class, $optionsNbSlides)
                ->add('autoplay', ChoiceType::class, $optionsAutoplay)
                ->add('fleches', ChoiceType::class, $optionsFleches)
                ->add('points', ChoiceType::class, $optionsPoints)
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
        });
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