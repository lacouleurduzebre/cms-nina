<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 13:35
 */

namespace App\Blocs\Actualites;


use App\Entity\Categorie;
use App\Entity\Page;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Twig\Environment;

class ActualitesTwig extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, Environment $twig)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('actualites', array($this, 'actualites')),
        );
    }

    public function actualites($limite = null, $idCategorie = null)
    {
        if($limite === null && $idCategorie === null){//Pas de limite ni de catégorie
            $repoPage = $this->doctrine->getRepository(Page::class);
            $pages = $repoPage->findAll();
        }elseif($limite != null && $idCategorie != null){//Limite et catégorie
            $categorie = $this->doctrine->getRepository(Categorie::class)->find($idCategorie);
            $pages = $categorie->getPages();
        }elseif($limite != null){//Uniquement limite
            $repoPage = $this->doctrine->getRepository(Page::class);
            $pages = $repoPage->findBy(array(), array('datePublication' => 'DESC'), $limite);
        }else{//Uniquement catégorie
            $categorie = $this->doctrine->getRepository(Categorie::class)->find($idCategorie);
            $pages = $categorie->getPages();
        }

        return $pages;
    }
}