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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CaseType extends AbstractType
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
            ->add('position', HiddenType::class, array(
            ))
            ->add('type', ChoiceType::class, array(
                "label" => false,
                "choices" => [
                    'Aperçu d\'une page' => 'page',
                    'Édition manuelle' => 'autre',
                ],
                'expanded' => true,
                "required" => true
            ))
            ->add('page', ChoiceType::class, array(
                'choices' => $pages,
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