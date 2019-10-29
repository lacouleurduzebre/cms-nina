<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\LEI;


use App\Form\Type\LimiteType;
use App\Form\Type\PaginationType;
use App\Form\Type\ResultatsParPageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LEIType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fluxGenerique', TextType::class, array(
                'mapped' => false,
                'label' => 'Flux générique',
                'help' => 'Commun à tous les blocs LEI'
            ))
            ->add('utiliserFluxSpecifique', ChoiceType::class, [
                'choices' => [
                    'Utiliser un flux spécifique' => 1
                ],
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'label' => false
            ])
            ->add('flux', TextType::class, array(
                'label' => 'Flux spécifique',
            ))
            ->add('clause', TextType::class)
            ->add('autresParametres', TextType::class, [
                'label' => 'Autres paramètres'
            ])
            ->add('clef_moda', TextType::class, array(
                'label' => 'Limiter à la clé de modalité :',
                'help' => "Filtrer les résultats pour ne conserver que les fiches répondant à ce critère",
                'required' => false
            ))
            ->add('limite', LimiteType::class)
            ->add('pagination', PaginationType::class)
            ->add('resultatsParPage', ResultatsParPageType::class);
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