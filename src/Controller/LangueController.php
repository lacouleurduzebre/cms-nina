<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 12/09/2017
 * Time: 10:49
 */

namespace App\Controller;


use App\Entity\Langue;
use App\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LangueController extends Controller
{
    /**
     * @Route("/langue/{id}", name="changerLangue")
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function changerAction($id, $idPage = null, Request $request){
        $repoLangue = $this->getDoctrine()->getRepository(Langue::class);

        $nvLangue = $repoLangue->find($id);
        $nvlocale = $nvLangue->getAbreviation();
        $request->getSession()->set('_locale', $nvlocale);

        if(isset($idPage)){
            $locale = $request->getLocale();
            $ancienneLangue = $repoLangue->findOneBy(array('abreviation' => $locale));

            $repoPage = $this->getDoctrine()->getRepository(Page::class);
            $page = $repoPage->find($idPage);

            if($page == $ancienneLangue->getPageAccueil()){//Si c'est la page d'accueil on va à l'accueil
                return $this->redirectToRoute('accueilLocale', array('_locale' => $nvlocale));
            }

            //A modifier
            return $this->redirectToRoute('voirPage', array('url' => $page->getSEO->getUrl()));
        }else{
            return $this->redirectToRoute('accueilLocale', array('_locale' => $nvlocale));
        }
    }
}