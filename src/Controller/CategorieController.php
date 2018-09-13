<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 31/08/2017
 * Time: 14:23
 */

namespace App\Controller;


use App\Entity\Categorie;
use App\Entity\TypeCategorie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends Controller
{
    /**
     * @Route("/{_locale}/{urlTypeCategorie}", name="voirTypeCategorie")
     * @param $urlTypeCategorie
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function voirTypeCategorieAction($_locale, $urlTypeCategorie){
        $em = $this->getDoctrine()->getManager();

        $typeCategorie = $em->getRepository(TypeCategorie::class)->findOneBy(array('url'=>$urlTypeCategorie));

        if($typeCategorie){
            $categories = $typeCategorie->getCategories();

            return $this->render('front/typeCategorie.html.twig', compact('typeCategorie', 'categories'));
        }else{
            $reponse = $this->forward('App\Controller\PageController::voirAction', array(
                'url'  => $urlTypeCategorie
            ));

            return $reponse;
        }
    }

    /**
     * @Route("/{_locale}/{urlTypeCategorie}/{urlCategorie}", name="voirCategorie")
     * @param $_locale
     * @param $urlTypeCategorie
     * @param $urlCategorie
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function voirCategorieAction($_locale, $urlTypeCategorie, $urlCategorie){
        $em = $this->getDoctrine()->getManager();

        $typeCategorie = $em->getRepository(TypeCategorie::class)->findOneBy(array('url'=>$urlTypeCategorie));
        $categorie = $em->getRepository(Categorie::class)->findOneBy(array('url'=>$urlCategorie));

        if($categorie->getTypeCategorie() == $typeCategorie){
            $pages = $categorie->getPages();

            return $this->render('front/categorie.html.twig', compact('pages', 'categorie'));
        }else{
            throw new NotFoundHttpException('Cette page n\'existe pas ou a été supprimée');
        }
    }
}