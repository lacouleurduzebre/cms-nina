<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-08-30
 * Time: 14:10
 */

namespace App\Form\Type;


use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $repoRole = $this->em->getRepository(Role::class);
        $objetsRoles = $repoRole->findAll();
        $roles = [];
        foreach($objetsRoles as $objetRole){
            $roles[$objetRole->getNom()] = $objetRole->getNom();
        }

        $resolver->setDefaults(array(
            'required' => false,
            'multiple' => true,
            'expanded' => true,
            'choices' => $roles
        ));
    }

    public function getParent(){
        return ChoiceType::class;
    }
}