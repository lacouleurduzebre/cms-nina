<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 15/08/2018
 * Time: 11:43
 */

namespace App\Form\Type;


use App\Entity\Langue;
use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TraductionsType extends AbstractType
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $repoPage = $this->em->getRepository(Page::class);

        $repoLangue = $this->em->getRepository(Langue::class);
        $langues = $repoLangue->findAll();

        foreach ($langues as $langue){
            $pages = $repoPage->findBy(array('langue' => $langue));
            $titres = [];
            foreach($pages as $page){
                $titres[$page->getTitre()] = $page->getId();
            }

            $builder->add($langue->getId(), ChoiceType::class, array(
                'label' => 'Page traduite en '.$langue->getNom(),
                'required' => false,
                'choices' => $titres
            ));
        }
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