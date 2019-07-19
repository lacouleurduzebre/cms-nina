<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 25/08/2017
 * Time: 13:59
 */

namespace App\Controller;


use App\Entity\Configuration;
use App\Service\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends Controller
{
    /**
     * @Route("/", name="accueil")
     * @Route("/{_locale}", name="accueilLocale", requirements={
     *     "_locale"="^[A-Za-z]{1,2}$"
     * })
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Page $spage, \App\Service\Langue $slangue, $_locale = null)
    {
        //Test route : locale ou non
        $redirection = $slangue->redirectionLocale('accueil', $_locale);
        if($redirection){
            return $redirection;
        }

        //Marquer le body
        $accueil = 'accueil';

        $page = $spage->getPageActive();
        if(!($page instanceof \App\Entity\Page)){
            return $page;
        }

        return $this->render('front/accueil.html.twig', array('page'=>$page, 'accueil'=>$accueil));
    }
}