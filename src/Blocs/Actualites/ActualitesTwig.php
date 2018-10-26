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
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
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

        //Pas de limite ni de catégorie
        if($limite === null && $categorie === null){
            if($pagination == 1){
                if(!isset($_GET['page'])){
                    $pages = $repoPage->pagesPubliees($langue, $resultatsParPage);
                }else{
                    $offset = ($_GET['page'] - 1) * $resultatsParPage;
                    $pages = $repoPage->pagesPubliees($langue, $resultatsParPage, $offset);
                }
            }else{
                $pages = $repoPage->pagesPubliees($langue);
            }

            return $pages;
        }


        //Limite et catégorie
        if($limite != null && $categorie != null){
            if($pagination == 1 && $resultatsParPage < $limite){
                if(!isset($_GET['page'])){
                    $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, $resultatsParPage);
                }else{
                    $offset = ($_GET['page'] - 1) * $resultatsParPage;
                    if($_GET['page'] * $resultatsParPage < $limite){
                        $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, $resultatsParPage, $offset);
                    }else{//Il reste moins de résultats à afficher que le nb de résultats par page
                        $nvLimite = $limite - $offset;
                        $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, $nvLimite, $offset);
                    }
                }
            }else{
                $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, $limite);
            }

            return $pages;
        }

        //Uniquement limite
        if($limite != null){
            if($pagination == 1 && $resultatsParPage < $limite){
                if(!isset($_GET['page'])){
                    $pages = $repoPage->pagesPubliees($langue, $resultatsParPage);
                }else{
                    $offset = ($_GET['page'] - 1) * $resultatsParPage;
                    if($_GET['page'] * $resultatsParPage < $limite){
                        $pages = $repoPage->pagesPubliees($langue, $resultatsParPage, $offset);
                    }else{//Il reste moins de résultats à afficher que le nb de résultats par page
                        $nvLimite = $limite - $offset;
                        $pages = $repoPage->pagesPubliees($langue, $nvLimite, $offset);
                    }
                }
            }else{
                $pages = $repoPage->pagesPubliees($langue, $limite);
            }

            return $pages;
        }

        //Uniquement catégorie (else)
        if($pagination == 1){
            if(!isset($_GET['page'])){
                $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, $resultatsParPage);
            }else{
                $offset = ($_GET['page'] - 1) * $resultatsParPage;
                $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, $resultatsParPage, $offset);
            }
        }else{
            $pages = $repoPage->pagesPublieesCategorie($categorie, $langue);
        }

        return $pages;
    }
}