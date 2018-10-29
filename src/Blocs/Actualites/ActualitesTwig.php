<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 13:35
 */

namespace App\Blocs\Actualites;


use App\Entity\Bloc;
use App\Entity\Categorie;
use App\Entity\Page;
use App\Service\Pagination;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class ActualitesTwig extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, Environment $twig, Pagination $pagination)
    {
        $this->doctrine = $doctrine;
        $this->pagination = $pagination;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('actualites', array($this, 'actualites')),
        );
    }

    public function actualites($langue, $bloc)
    {
        $parametres = $bloc->getContenu();
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
        if($limite === null && $categorie === null){
//            $resultats['if'] = 1;
            $nbResultats = $repoPage->nombrePagesPubliees($langue);
            if($pagination == 1){
                if(!isset($_GET['page'])){
                    $pages = $repoPage->pagesPubliees($langue, $resultatsParPage);
                }else{
                    $pageActuelle = $this->pagination->testPageActuelle($_GET['page'], $nbResultats, $resultatsParPage);
                    $offset = ($pageActuelle - 1) * $resultatsParPage;
                    $pages = $repoPage->pagesPubliees($langue, $resultatsParPage, $offset);
                }
            }else{
                $pages = $repoPage->pagesPubliees($langue);
            }
        }


        //Limite et catégorie
        elseif($limite != null && $categorie != null){
//            $resultats['if'] = 2;
            $nbResultats = $repoPage->nombrePagesPublieesCategorie($categorie, $langue, $limite);
            if($pagination == 1 && $resultatsParPage < $limite){
                if(!isset($_GET['page'])){
                    $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, $resultatsParPage);
                }else{
                    $pageActuelle = $this->pagination->testPageActuelle($_GET['page'], $nbResultats, $resultatsParPage);
                    $offset = ($pageActuelle - 1) * $resultatsParPage;
                    if($pageActuelle * $resultatsParPage < $limite){
                        $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, $resultatsParPage, $offset);
                    }else{//Il reste moins de résultats à afficher que le nb de résultats par page
                        $nvLimite = $limite - $offset;
                        $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, $nvLimite, $offset);
                    }
                }
            }else{
                $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, $limite);
            }
        }

        //Uniquement limite
        elseif($limite != null){
//            $resultats['if'] = 3;
            $nbResultats = $repoPage->nombrePagesPubliees($langue, $limite);
            if($pagination == 1 && $resultatsParPage < $limite){
                if(!isset($_GET['page'])){
                    $pages = $repoPage->pagesPubliees($langue, $resultatsParPage);
                }else{
                    $pageActuelle = $this->pagination->testPageActuelle($_GET['page'], $nbResultats, $resultatsParPage);
                    $offset = ($pageActuelle - 1) * $resultatsParPage;
                    if($pageActuelle * $resultatsParPage < $limite){
                        $pages = $repoPage->pagesPubliees($langue, $resultatsParPage, $offset);
                    }else{//Il reste moins de résultats à afficher que le nb de résultats par page
                        $nvLimite = $limite - $offset;
                        $pages = $repoPage->pagesPubliees($langue, $nvLimite, $offset);
                    }
                }
            }else{
                $pages = $repoPage->pagesPubliees($langue, $limite);
            }
        }

        //Uniquement catégorie (else)
        else{
//            $resultats['if'] = 4;
            $nbResultats = $repoPage->nombrePagesPublieesCategorie($categorie, $langue);
            if($pagination == 1){
                if(!isset($_GET['page'])){
                    $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, $resultatsParPage);
                }else{
                    $pageActuelle = $this->pagination->testPageActuelle($_GET['page'], $nbResultats, $resultatsParPage);
                    $offset = ($pageActuelle - 1) * $resultatsParPage;
                    $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, $resultatsParPage, $offset);
                }
            }else{
                $pages = $repoPage->pagesPublieesCategorie($categorie, $langue);
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