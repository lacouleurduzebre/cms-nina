<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 25/08/2017
 * Time: 13:59
 */

namespace App\Controller;


use App\Entity\Langue;
use App\Service\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends Controller
{
    /**
     * @Route("/", name="accueil")
     * @Route("/{_locale}", name="accueilLocale")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Page $spage, $_locale = null)
    {
        //Test route
        $repoLangue = $this->getDoctrine()->getRepository(Langue::class);
        $nbLangues = $repoLangue->nombreTotal();

        if(isset($_locale) && $nbLangues == 1){
            return new RedirectResponse($this->generateUrl('accueil'), 301);
        }elseif(!isset($_locale) && $nbLangues > 1){
            $langueDefaut = $repoLangue->findOneBy(array('defaut' => 1))->getAbreviation();
            return new RedirectResponse($this->generateUrl('accueilLocale', array('_locale' => $langueDefaut)), 301);
        }
        //Fin test route

        $accueil = 'accueil';//Marquer le body

        $page = $spage->getPageActive();
        if(!($page instanceof \App\Entity\Page)){
            return $page;
        }

        return $this->render('front/accueil.html.twig', array('page'=>$page, 'accueil'=>$accueil));
    }
}