<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\Page;


use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
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
            ->add('page', ChoiceType::class, array(
                'choices' => $pages
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