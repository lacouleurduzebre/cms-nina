<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 10:19
 */

namespace App\Blocs\Categorie;


use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CategorieTwig extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('pagesDeLaCategorie', array($this, 'pagesDeLaCategorie')),
//            new \Twig_SimpleFunction('infosCategorie', array($this, 'infosCategorie')),
        );
    }

    public function pagesDeLaCategorie($idCategorie)
    {
        $repoCategorie = $this->doctrine->getRepository(Categorie::class);
        $categorie = $repoCategorie->find($idCategorie);
        if(!$categorie){
            return false;
        }

        $pages = $categorie->getPages();

        return $pages->toArray();
    }

    /*public function infosCategorie($idCategorie)
    {
        $repoCategorie = $this->doctrine->getRepository(Categorie::class);
        $categorie = $repoCategorie->find($idCategorie);

        return array('categorie'=>$categorie);
    }*/
}