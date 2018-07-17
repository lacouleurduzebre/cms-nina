<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Modules\Categorie;


use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function __construct(EntityManagerInterface $em)
    {
     $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $repoCategorie = $this->em->getRepository(Categorie::class);
        $objetsCategories = $repoCategorie->findAll();
        $categories = [];
        foreach($objetsCategories as $objetCategorie){
            $categories[$objetCategorie->getNom()] = $objetCategorie->getId();
        }

        $builder
            ->add('categorie', ChoiceType::class, array(
                'choices' => $categories
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