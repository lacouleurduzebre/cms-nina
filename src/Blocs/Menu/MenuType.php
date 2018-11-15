<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\Menu;


use App\Entity\Langue;
use App\Entity\Menu;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    public function __construct(EntityManagerInterface $em)
    {
     $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $repoLangue = $this->em->getRepository(Langue::class);
        $langues = $repoLangue->findAll();

        $repoMenu = $this->em->getRepository(Menu::class);
        $objetsMenus = $repoMenu->findAll();
        $menus = [];
        foreach($objetsMenus as $objetMenu){
            if(count($langues) > 1){
                $menus[$objetMenu->getLangue()->getAbreviation().' - '.$objetMenu->getNom()] = $objetMenu->getId();
            }else{
                $menus[$objetMenu->getNom()] = $objetMenu->getId();
            }
        }

        $builder
            ->add('menu', ChoiceType::class, array(
                'choices' => $menus
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