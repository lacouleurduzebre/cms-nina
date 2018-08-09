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
     * @Route("/sitemap.xml", name="sitemap")
     */
    public function sitemapAction(Request $request){
        $repoPage = $this->getDoctrine()->getRepository(Page::class);
        $repoLangue = $this->getDoctrine()->getRepository(Langue::class);

        $locale = $request->getLocale();
        $langue = $repoLangue->findOneBy(array('abreviation' => $locale));

        $pages = $repoPage->pagesPubliees($langue);

        return $this->render('front/sitemap.xml.twig', array('pages'=>$pages));
    }
}