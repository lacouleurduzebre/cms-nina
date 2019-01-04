<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\Categorie;


use App\Entity\Categorie;
use App\Form\Type\LimiteType;
use App\Form\Type\PaginationType;
use App\Form\Type\ResultatsParPageType;
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
        $objetsCategories = $repoCategorie->findBy(array(), array('nom' => 'ASC'));
        $categories = [];
        $categories['Toutes'] = 0;
        foreach($objetsCategories as $objetCategorie){
            $categories[$objetCategorie->getNom()] = $objetCategorie->getId();
        }

        $builder
            ->add('categorie', ChoiceType::class, array(
                'choices' => $categories
            ))
            ->add('limite', LimiteType::class)
            ->add('pagination', PaginationType::class)
            ->add('resultatsParPage', ResultatsParPageType::class);
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