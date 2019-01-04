<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 29/10/2018
 * Time: 09:32
 */

namespace App\Service;


use Twig\Environment;

class Pagination
{
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function testPageActuelle($page, $nbResultats, $nbResultatsParPage){
        $nbPages = $this->round_up(($nbResultats / $nbResultatsParPage), 1);

        if($page > $nbPages){
            $page = $nbPages;
        }

        return $page;
    }

    public function renderPagination($nbResultats, $nbResultatsParPage, $page){
        $nbPages = $this->round_up(($nbResultats / $nbResultatsParPage), 1);

        if($nbPages == 1){
            return null;
        }

        return $this->twig->render('front/pagination.html.twig', array('nbPages' => $nbPages, 'page' => $page));
    }

    private function round_up($number, $precision = 2)
    {
        $fig = (int) str_pad('1', $precision, '0');
        return (ceil($number * $fig) / $fig);
    }

    public function getPagination($donnees, $parametres, $page){
        //Création des variables
        $limite = isset($parametres['limite']) ? $parametres['limite'] : null;
        $pagination = isset($parametres['pagination'][0]) ? $parametres['pagination'][0] : 0;
        $resultatsParPage = isset($parametres['resultatsParPage']) ? $parametres['resultatsParPage'] : 9;

        $resultats = [];

        //Diminution du nombre de résultats à $limite
        if($limite != null){
            $donnees = array_splice($donnees, 0, $limite);
        }

        $nbResultats = count($donnees);
        $resultats['pagination'] = null;

        //Pagination
        if($pagination == 1) {
            if($page == 1){
                $donnees = array_splice($donnees, 0, $resultatsParPage);
            }else{
                $page = $this->testPageActuelle($page, $nbResultats, $resultatsParPage);
                $offset = ($page - 1) * $resultatsParPage;
                $donnees = array_splice($donnees, $offset, $resultatsParPage);
            }
            $resultats['pagination'] = $this->renderPagination($nbResultats, $resultatsParPage, $page);
        }

        $resultats['donnees'] = $donnees;
        $resultats['nbResultats'] = $nbResultats;

        return $resultats;
    }
}