<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 25/08/2017
 * Time: 13:59
 */

namespace App\Controller;


use App\Entity\Langue;
use App\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends Controller
{
    /**
     * @Route("/", name="accueil")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $accueil = 'accueil';//Marquer le body

        $locale = $request->getLocale();

        $repositoryLangue=$this->getDoctrine()->getManager()->getRepository(Langue::class);
        $langue = $repositoryLangue->findOneBy(array('abreviation' => $locale));

        $repositoryPage = $this->getDoctrine()->getManager()->getRepository(Page::class);
        $pages = $repositoryPage->pagesPubliees($langue);

        return $this->render('front/accueil.html.twig', array('pages'=>$pages, 'accueil'=>$accueil));
    }
}