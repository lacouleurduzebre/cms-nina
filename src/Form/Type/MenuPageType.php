<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 29/06/2018
 * Time: 13:12
 */

namespace App\Form\Type;


use App\Repository\PageRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuPageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('page', EntityType::class, array(
                'class' => 'App\Entity\Page',
                'choice_label' => 'titre',
                'query_builder' => function (PageRepository $pageRepository) {
                    return $pageRepository->pagesPublieesQB();
                },
            ))
            ->add('position', IntegerType::class, array('empty_data' => '0'))
            ->add('pageParent', EntityType::class, array('class' => 'App\Entity\Page', 'choice_label' => 'titre', 'required' => false));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\MenuPage'
        ));
    }

    public function getParent(){
        return FormType::class;
    }
}