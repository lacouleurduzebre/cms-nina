<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 25/08/2017
 * Time: 13:59
 */

namespace App\Controller;


use App\Entity\Configuration;
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
    public function indexAction()
    {
        $accueil = 'accueil';//Marquer le body

        $repoConfiguration = $this->getDoctrine()->getRepository(Configuration::class);
        $configuration = $repoConfiguration->find(1);

        $page = $configuration->getPageAccueil();

        return $this->render('front/accueil.html.twig', array('page'=>$page, 'accueil'=>$accueil));
    }
}