<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 10:19
 */

namespace App\Blocs\TypeCategorie;


use App\Controller\SEOController;
use App\Entity\Categorie;
use App\Service\Page;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

class TypeCategorieTwig extends \Twig_Extension
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('categoriesDeType', array($this, 'categoriesDeType')),
            new \Twig_SimpleFunction('pagesDeType', array($this, 'pagesDeType')),
        );
    }

    public function categoriesDeType($idTypeCategorie, $limite = null)
    {
        $repoCategorie = $this->doctrine->getRepository(Categorie::class);
        $categories = $repoCategorie->findBy(array('typeCategorie' => $idTypeCategorie), array('nom' => 'ASC'), $limite);
        if(!$categories){
            return false;
        }

        return $categories;
    }

    public function pagesDeType($idTypeCategorie, $limite = null)
    {
        $repoCategorie = $this->doctrine->getRepository(Categorie::class);
        $categories = $repoCategorie->findBy(array('typeCategorie' => $idTypeCategorie));

        if(!$categories){
            return false;
        }

        $pages = new ArrayCollection();

        foreach($categories as $categorie){
            $pagesCategorie = $categorie->getPages();
            $pages = new ArrayCollection(
                array_unique(array_merge($pages->toArray(), $pagesCategorie->toArray()))
            );
        }

        $pages = $pages->toArray();

        //Pages non publiées supprimées de la liste
        foreach($pages as $id => $page){
            if(!Page::isPublie($page)){
                unset($pages[$id]);
            }
        }

        if($limite){
            $pages = array_slice($pages, 0, $limite);
        }

        usort($pages, array($this, 'triTitre'));

        return $pages;
    }

    public function triTitre($a, $b){
        $titreA = SEOController::slugify($a->getTitre(), ' ');
        $titreB = SEOController::slugify($b->getTitre(), ' ');

        if ($titreA == $titreB) {
            return 0;
        }
        return ($titreA < $titreB) ? -1 : 1;
    }
}