<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 07/09/2018
 * Time: 15:53
 */

namespace App\Blocs\Galerie;


use App\Entity\Page;
use App\Form\Type\ImageDefautType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalerieImageType extends AbstractType
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $repoPage = $this->em->getRepository(Page::class);
        $objetsPages = $repoPage->findAll();
        $pages = [];

        foreach($objetsPages as $objetPage){
            $pages[$objetPage->getTitre()] = $objetPage->getId();
        }

        $builder->add('image', ImageDefautType::class, array(
            'label' => false,
        ))
        ->add('lien', TextType::class, array(
            'label' => 'Lien',
            "help" => "Pour les liens externes, ne pas oublier d'ajouter le préfixe http:// ou https://"
        ))
        ->add('page', ChoiceType::class, array(
            "label" => "Page",
            "choices" => $pages,
            "required" => false
        ))
        ->add('blank', ChoiceType::class, array(
            'label' => false,
            'multiple' => true,
            'expanded' => true,
            'choices' => array(
                'Ouvrir le lien dans un nouvel onglet' => 1
            )
        ))
        ->add('position', HiddenType::class, array(
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