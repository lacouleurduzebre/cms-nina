<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 01/03/2018
 * Time: 11:07
 */

namespace App\Form\Type;


use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageParentType extends AbstractType
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if(isset($_GET['idPageParent'])){
            $builder
                ->add('pageParent', EntityType::class, array('class' => 'App\Entity\Page', 'nullable' => true, 'choice_label' => 'titre', 'data' => $this->em->getReference(Page::class, $_GET['idPageParent'])));
        }else{
            $builder
                ->add('pageParent', EntityType::class, array('class' => 'App\Entity\Page', 'choice_label' => 'titre'));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Page'
        ));
    }

    public function getParent(){
        return FormType::class;
    }
}