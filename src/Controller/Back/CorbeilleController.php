<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 01/09/2017
 * Time: 13:43
 */

namespace App\Controller\Back;


use App\Entity\Commentaire;
use App\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class CorbeilleController extends Controller
{
    /**
     * @Route("/admin/corbeille", name="corbeille")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listeAction(){
        $em = $this->get('doctrine')->getManager();

        $pagesCorbeille = $em->getRepository(Page::class)->findByCorbeille('1');
        $commentairesCorbeille = $em->getRepository(Commentaire::class)->findByCorbeille('1');

        return $this->render('back:corbeille:liste.html.twig', array(
            'pagesCorbeille' => $pagesCorbeille,
            'commentairesCorbeille' => $commentairesCorbeille
        ));
    }
}