<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 07/09/2018
 * Time: 15:53
 */

namespace App\Blocs\Grille;


use App\Blocs\Bouton\BoutonType;
use App\Entity\Page;
use App\Form\Type\ImageDefautType;
use App\Repository\PageRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('position', HiddenType::class, array(
            ))
            ->add('page', EntityType::class, array(
                'class' => Page::class,
                'query_builder' => function (PageRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.titre', 'ASC');
                },
                'required' => false,
                'label' => 'Page',
                'help' => "Si vous choisissez d'afficher une page, son titre, son résumé et sa vignette seront utilisées"
            ))
            ->add('titre', TextType::class, array(
                'label' => 'Titre'
            ))
            ->add('texte', TextareaType::class, array(
                'label' => 'Texte'
            ))
            ->add('lien', BoutonType::class, array(
                'required' => false,
                'label' => 'Lien'
            ))
            ->add('image', ImageDefautType::class, array(
                'label' => 'Image'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'label' => false
        ));
    }

    public function getParent(){
        return FormType::class;
    }
}