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
}