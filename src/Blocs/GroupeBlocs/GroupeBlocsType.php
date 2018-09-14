<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\GroupeBlocs;


use App\Entity\GroupeBlocs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupeBlocsType extends AbstractType
{
    public function __construct(EntityManagerInterface $em)
    {
     $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $repoGroupeBlocs = $this->em->getRepository(GroupeBlocs::class);
        $objetsGroupesBlocs = $repoGroupeBlocs->findAll();
        $groupeBlocs = [];
        foreach($objetsGroupesBlocs as $objetGroupesBlocs){
            $groupeBlocs[$objetGroupesBlocs->getNom()] = $objetGroupesBlocs->getId();
        }

        $builder
            ->add('groupeBlocs', ChoiceType::class, array(
                'choices' => $groupeBlocs
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