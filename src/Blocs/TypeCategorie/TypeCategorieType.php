<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\TypeCategorie;


use App\Entity\TypeCategorie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeCategorieType extends AbstractType
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $repoTypeCategorie = $this->em->getRepository(TypeCategorie::class);
        $objetsTypesCategories = $repoTypeCategorie->findAll();
        $typesCategories = [];
        foreach($objetsTypesCategories as $objetTypeCategorie){
            $typesCategories[$objetTypeCategorie->getNom()] = $objetTypeCategorie->getId();
        }

        $builder
            ->add('typeCategorie', ChoiceType::class, array(
                'choices' => $typesCategories,
                'label' => 'Type de catégorie'
            ))
            ->add('affichage', ChoiceType::class, array(
                'choices' => array(
                    'Catégories' => 'categories',
                    'Pages' => 'pages'
                ),
                'label' => 'Contenu à afficher',
                'expanded' => true
            ))
            ->add('limite', NumberType::class, array(
                'label' => 'Nombre limite de résultats',
                'help' => "Si aucune limite n'est précisée, tous les résultats seront affichés",
                'required' => false
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