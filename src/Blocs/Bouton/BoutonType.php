<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\Bouton;


use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoutonType extends AbstractType
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

        $builder
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
            ))
            ->add('texte', TextType::class, array(
                "label" => "Texte du lien",
                "help" => "Par défaut : le titre de la page, ou \"Voir la page\""
            ))
            ->add('titre', TextType::class, array(
                "label" => "Titre au survol",
                "required" => false,
                "help" => null
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