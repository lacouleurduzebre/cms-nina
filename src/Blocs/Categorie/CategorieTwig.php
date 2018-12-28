<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 10:19
 */

namespace App\Blocs\Categorie;


use App\Entity\Categorie;
use App\Entity\Page;
use App\Service\Pagination;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CategorieTwig extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, Pagination $pagination)
    {
        $this->doctrine = $doctrine;
        $this->pagination = $pagination;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('pagesDeLaCategorie', array($this, 'pagesDeLaCategorie')),
//            new \Twig_SimpleFunction('infosCategorie', array($this, 'infosCategorie')),
        );
    }

    public function pagesDeLaCategorie($langue, $parametres)
    {
        $limite = $parametres['limite'];
        $categorie = $parametres['categorie'];
        if(isset($parametres['pagination'][0])){
            $pagination = $parametres['pagination'][0];
        }else{
            $pagination = null;
        };
        $resultatsParPage = $parametres['resultatsParPage'];

        $repoPage = $this->doctrine->getRepository(Page::class);

        $resultats = [];
        //Pas de limite ni de catégorie
        if($limite === null && $categorie == 0){
//            $resultats['if'] = 1;
            $nbResultats = $repoPage->pagesPublieesCategorie($categorie, $langue, false);
            if($pagination == 1){
                if(!isset($_GET['page'])){
                    $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, true, $resultatsParPage);
                }else{
                    $pageActuelle = $this->pagination->testPageActuelle($_GET['page'], $nbResultats, $resultatsParPage);
                    $offset = ($pageActuelle - 1) * $resultatsParPage;
                    $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, true, $resultatsParPage, $offset);
                }
            }else{
                $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, true);
            }
        }

        //Limite et catégorie
        elseif($limite != null && $categorie != null){
//            $resultats['if'] = 2;
            $nbResultats = $repoPage->pagesPublieesCategorie($categorie, $langue, false, $limite);
            if($pagination == 1 && $resultatsParPage < $limite){
                if(!isset($_GET['page'])){
                    $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, true, $resultatsParPage);
                }else{
                    $pageActuelle = $this->pagination->testPageActuelle($_GET['page'], $nbResultats, $resultatsParPage);
                    $offset = ($pageActuelle - 1) * $resultatsParPage;
                    if($pageActuelle * $resultatsParPage < $limite){
                        $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, true, $resultatsParPage, $offset);
                    }else{//Il reste moins de résultats à afficher que le nb de résultats par page
                        $nvLimite = $limite - $offset;
                        $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, true, $nvLimite, $offset);
                    }
                }
            }else{
                $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, true, $limite);
            }
        }

        //Uniquement limite
        elseif($limite != null){
//            $resultats['if'] = 3;
            $nbResultats = $repoPage->pagesPublieesCategorie($categorie, $langue, false, $limite);
            if($pagination == 1 && $resultatsParPage < $limite){
                if(!isset($_GET['page'])){
                    $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, true, $resultatsParPage);
                }else{
                    $pageActuelle = $this->pagination->testPageActuelle($_GET['page'], $nbResultats, $resultatsParPage);
                    $offset = ($pageActuelle - 1) * $resultatsParPage;
                    if($pageActuelle * $resultatsParPage < $limite){
                        $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, true, $resultatsParPage, $offset);
                    }else{//Il reste moins de résultats à afficher que le nb de résultats par page
                        $nvLimite = $limite - $offset;
                        $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, true, $nvLimite, $offset);
                    }
                }
            }else{
                $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, true, $limite);
            }
        }

        //Uniquement catégorie (else)
        else{
//            $resultats['if'] = 4;
            $nbResultats = $repoPage->pagesPublieesCategorie($categorie, $langue, false);
            if($pagination == 1){
                if(!isset($_GET['page'])){
                    $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, true, $resultatsParPage);
                }else{
                    $pageActuelle = $this->pagination->testPageActuelle($_GET['page'], $nbResultats, $resultatsParPage);
                    $offset = ($pageActuelle - 1) * $resultatsParPage;
                    $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, true, $resultatsParPage, $offset);
                }
            }else{
                $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, true);
            }
        }

        $resultats['pages'] = $pages;
        $resultats['nbResultats'] = $nbResultats;
        if($pagination == 1){
            if(!isset($pageActuelle)){
                $pageActuelle = 1;
            }
            $resultats['pagination'] = $this->pagination->renderPagination($nbResultats, $resultatsParPage, $pageActuelle);
        }else{
            $resultats['pagination'] = null;
        }

        return $resultats;
    }
}