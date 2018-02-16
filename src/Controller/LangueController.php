<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 12/09/2017
 * Time: 10:49
 */

namespace App\Controller;


use App\Entity\Langue;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LangueController extends Controller
{
    /**
     * @Route("/langue/{id}", name="changerLangue")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changerAction($id, Request $request){
        $langue = $this->getDoctrine()->getManager()->find(Langue::class, $id)->getAbreviation();
        $request->getSession()->set('_locale', $langue);

        $session = $request->getSession();
        $session->set('referer', $request->headers->get('referer'));

        return $this->redirect($session->get('referer'));
    }
}