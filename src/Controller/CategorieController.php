<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 31/08/2017
 * Time: 14:23
 */

namespace App\Controller;


use App\Entity\Categorie;
use App\Entity\Langue;
use App\Entity\SEOCategorie;
use App\Entity\SEOTypeCategorie;
use App\Entity\TypeCategorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    /**
     * @Route("/categorie/{urlTypeCategorie}/{urlCategorie}", name="voirCategorie")
     * @Route("/{_locale}/categorie/{urlTypeCategorie}/{urlCategorie}", name="voirCategorieLocale", requirements={
     *     "_locale"="^[A-Za-z]{1,2}$"
     * })
     * @param $_locale
     * @param $urlTypeCategorie
     * @param $urlCategorie
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function voirCategorieAction(Request $request, \App\Service\Langue $slangue, $urlTypeCategorie, $urlCategorie, $_locale = null){
        //Test route : locale ou non
        $redirection = $slangue->redirectionLocale('voirCategorie', $_locale, array('urlTypeCategorie' => $urlTypeCategorie, 'urlCategorie' => $urlCategorie));
        if($redirection){
            return $redirection;
        }

        $em = $this->getDoctrine()->getManager();

        $SEOTypeCategorie = $em->getRepository(SEOTypeCategorie::class)->findOneBy(array('url'=>$urlTypeCategorie));
        $SEOCategorie = $em->getRepository(SEOCategorie::class)->findOneBy(array('url'=>$urlCategorie));

        if(!$SEOTypeCategorie or !$SEOCategorie){
            throw new NotFoundHttpException('Cette page n\'existe pas ou a été supprimée');
        }

        $typeCategorie = $SEOTypeCategorie->getTypeCategorie();
        $categorie = $SEOCategorie->getCategorie();

        if($categorie->getTypeCategorie() == $typeCategorie){
            $repoLangue = $this->getDoctrine()->getRepository(Langue::class);
            $locale = $request->getLocale();
            $langue = $repoLangue->findOneBy(array('abreviation' => $locale));

            if($categorie->getLangue() != $langue){
                throw new NotFoundHttpException('Cette page n\'existe pas ou a été supprimée');
            }

            $pages = $categorie->getPages();

            return $this->render('front/categorie.html.twig', compact('pages', 'categorie'));
        }else{
            throw new NotFoundHttpException('Cette page n\'existe pas ou a été supprimée');
        }
    }

    /**
     * @Route("/categorie/{urlTypeCategorie}", name="voirTypeCategorie")
     * @Route("/{_locale}/categorie/{urlTypeCategorie}", name="voirTypeCategorieLocale", requirements={
     *     "_locale"="^[A-Za-z]{1,2}$"
     * })
     * @param $urlTypeCategorie
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function voirTypeCategorieAction(Request $request, \App\Service\Langue $slangue, $urlTypeCategorie, $_locale = null){
        //Test route : locale ou non
        $redirection = $slangue->redirectionLocale('voirTypeCategorie', $_locale, array('urlTypeCategorie' => $urlTypeCategorie));
        if($redirection){
            return $redirection;
        }

        $em = $this->getDoctrine()->getManager();

        $SEOTypeCategorie = $em->getRepository(SEOTypeCategorie::class)->findOneBy(array('url'=>$urlTypeCategorie));

        if(!$SEOTypeCategorie){
            throw new NotFoundHttpException('Cette page n\'existe pas ou a été supprimée');
        }

        $typeCategorie = $SEOTypeCategorie->getTypeCategorie();

        if($typeCategorie){
            $repoLangue = $this->getDoctrine()->getRepository(Langue::class);
            $locale = $request->getLocale();
            $langue = $repoLangue->findOneBy(array('abreviation' => $locale));

            if($typeCategorie->getLangue() != $langue){
                throw new NotFoundHttpException('Cette page n\'existe pas ou a été supprimée');
            }

            $categories = $typeCategorie->getCategories();

            return $this->render('front/typeCategorie.html.twig', compact('typeCategorie', 'categories'));
        }else{
            throw new NotFoundHttpException('Cette page n\'existe pas ou a été supprimée');
        }
    }
}