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
     * @Route("/{urlTypeCategorie}", name="voirTypeCategorie")
     * @param $urlTypeCategorie
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function voirTypeCategorieAction($urlTypeCategorie){
        $em = $this->getDoctrine()->getManager();

        $typeCategorie = $em->getRepository(TypeCategorie::class)->findOneBy(array('url'=>$urlTypeCategorie));

        if($typeCategorie){
            $categories = $typeCategorie->getCategories();

            return $this->render('front/voirTypeCategorie.html.twig', compact('typeCategorie', 'categories'));
        }else{
            $reponse = $this->forward('App\Controller\PageController::voirAction', array(
                'url'  => $urlTypeCategorie
            ));

            return $reponse;
        }
    }

    /**
     * @Route("/{urlTypeCategorie}/{urlCategorie}", name="voirCategorie")
     * @param $urlTypeCategorie
     * @param $urlCategorie
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function voirCategorieAction($urlTypeCategorie, $urlCategorie){
        $em = $this->getDoctrine()->getManager();

        $typeCategorie = $em->getRepository(TypeCategorie::class)->findOneBy(array('url'=>$urlTypeCategorie));
        $categorie = $em->getRepository(Categorie::class)->findOneBy(array('url'=>$urlCategorie));

        if($categorie->getTypeCategorie() == $typeCategorie){
            $pages = $categorie->getPages();

            return $this->render('front/voirCategorie.html.twig', compact('pages', 'categorie'));
        }else{
            throw new NotFoundHttpException('Cette page n\'existe pas ou a été supprimée');
        }
    }
}