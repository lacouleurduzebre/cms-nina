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
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        //Installeur
        $connexion = $this->getDoctrine()->getConnection()->isConnected();
        if(!$connexion){
            return $this->redirectToRoute('installeur', ['etape' => 1]);
        }
        //Fin installeur

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