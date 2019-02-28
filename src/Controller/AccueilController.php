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
    public function indexAction($_locale = null, Page $spage)
    {
        $accueil = 'accueil';//Marquer le body

        $page = $spage->getPageActive();

        return $this->render('front/accueil.html.twig', array('page'=>$page, 'accueil'=>$accueil));
    }
}