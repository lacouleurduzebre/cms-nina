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
    public function changerAction($id, Request $request){
        $repoLangue = $this->getDoctrine()->getRepository(Langue::class);

        $nvLangue = $repoLangue->find($id);
        $nvlocale = $nvLangue->getAbreviation();

        $idPage = $request->get('idPage');

        if($idPage != null){
            $locale = $request->getLocale();
            $ancienneLangue = $repoLangue->findOneBy(array('abreviation' => $locale));

            $repoPage = $this->getDoctrine()->getRepository(Page::class);
            $page = $repoPage->find($idPage);

            $traductions = $page->getTraductions();

            if($page == $ancienneLangue->getPageAccueil() || $traductions[$id] == null){//Si c'est la page d'accueil on va à l'accueil
                return $this->redirectToRoute('accueilLocale', array('_locale' => $nvlocale));
            }

            //Sinon on cherche sa traduction
            $idPageTraduite = $traductions[$id];
            $pageTraduite = $repoPage->find($idPageTraduite);

            $request->getSession()->set('_locale', $nvlocale);

            return $this->redirectToRoute('voirPage', array('_locale' => $nvlocale, 'url' => $pageTraduite->getSEO()->getUrl()));
        }else{
            $request->getSession()->set('_locale', $nvlocale);

            return $this->redirectToRoute('accueilLocale', array('_locale' => $nvlocale));
        }
    }
}