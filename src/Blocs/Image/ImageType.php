<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\Image;


use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $repoPage = $this->em->getRepository(Page::class);
        $objetsPages = $repoPage->pagesPubliees();
        $pages = [];

        foreach($objetsPages as $objetPage){
            $pages[$objetPage->getTitre()] = $objetPage->getId();
        }

        $builder
            ->add('image', TextType::class, array(
                'required' => false,
            ))
            ->add('titre', TextType::class, array(
                'required' => false,
            ))
            ->add('description', TextType::class, array(
                'required' => false,
            ))
            ->add('hauteurMax', TextType::class)
            ->add('largeurMax', TextType::class)
            ->add('alignement', ChoiceType::class, [
                'choices' => [
                    "À gauche" => "gauche",
                    "Centré" => "centre",
                    "À droite" => "droite"
                ]
            ])
            ->add('lien', TextType::class, array(
                "help" => "Pour les liens externes, ne pas oublier d'ajouter le préfixe http:// ou https://",
                "label" => "Lien"
            ))
            ->add('page', ChoiceType::class, array(
                "label" => "Page",
                "choices" => $pages,
                "required" => false
            ))
            ->add('blank', ChoiceType::class, array(
                "choices" => array(
                    "Ouvrir le lien dans un nouvel onglet" => 1
                ),
                "required" => false,
                "expanded" => true,
                "multiple" => true,
                "label" => false
            ));
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