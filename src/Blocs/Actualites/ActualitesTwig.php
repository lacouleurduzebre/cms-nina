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

    public function actualites($langue, $limite = null, $idCategorie = null)
    {
        $repoPage = $this->doctrine->getRepository(Page::class);
        if($limite === null && $idCategorie === null){//Pas de limite ni de catégorie
            $pages = $repoPage->pagesPubliees($langue);
        }elseif($limite != null && $idCategorie != null){//Limite et catégorie
            $pages = $repoPage->pagesPublieesCategorie($idCategorie, $langue, $limite);
        }elseif($limite != null){//Uniquement limite
            $pages = $repoPage->pagesPubliees($langue, $limite);
        }else{//Uniquement catégorie
            $pages = $repoPage->pagesPublieesCategorie($idCategorie, $langue);
        }

        return $pages;
    }
}