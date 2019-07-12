<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 09/08/2018
 * Time: 15:48
 */

namespace App\Controller;


use App\Entity\Langue;
use App\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends Controller
{
    /**
     * @Route("/sitemap.xml", defaults={"_format"="xml"}, name="sitemap")
     * @Route("/{_locale}/sitemap.xml", defaults={"_format"="xml"}, name="sitemapLocale", requirements={
     *     "_locale"="^[A-Za-z]{1,2}$"
     * })
     */
    public function sitemapAction(\App\Service\Langue $slangue, $_locale = null){
        //Test route : locale ou non
        $redirection = $slangue->redirectionLocale('sitemap', $_locale);
        if($redirection){
            return $redirection;
        }

        $repoPage = $this->getDoctrine()->getRepository(Page::class);

        $repoLangue = $this->getDoctrine()->getRepository(Langue::class);
        $langue = $repoLangue->findOneBy(array('abreviation' => $_locale));
        if(!$langue){
            $langue = $repoLangue->findOneBy(array('defaut' => true));
        }

        $pages = $repoPage->pagesPubliees($langue);

        return $this->render('front/sitemap.xml.twig', array('pages'=>$pages));
    }
}