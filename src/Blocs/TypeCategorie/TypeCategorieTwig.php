<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 10:19
 */

namespace App\Blocs\TypeCategorie;


use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TypeCategorieTwig extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('categoriesDeType', array($this, 'categoriesDeType')),
        );
    }

    public function categoriesDeType($idTypeCategorie)
    {
        $repoCategorie = $this->doctrine->getRepository(Categorie::class);
        $categories = $repoCategorie->findBy(array('typeCategorie' => $idTypeCategorie));
        if(!$categories){
            return false;
        }

        return $categories;
    }
}