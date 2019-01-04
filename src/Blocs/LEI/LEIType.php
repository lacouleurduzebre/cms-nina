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
use Symfony\Component\Form\Extension\Core\Type\FormType;
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