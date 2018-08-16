<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 25/08/2017
 * Time: 13:59
 */

namespace App\Controller;


use App\Entity\Langue;
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
    public function indexAction($_locale = null, Request $request)
    {
        $accueil = 'accueil';//Marquer le body
        $repoLangue = $this->getDoctrine()->getRepository(Langue::class);

        if(isset($_locale)){
            $langue = $repoLangue->findOneBy(array('abreviation' => $_locale));

            if(!$langue){//Si l'utilisateur essaye de naviguer sur une langue qui n'existe page
                throw new NotFoundHttpException('Vous essayez de naviguer dans une langue non compatible avec ce site');
            }

            $locale = $request->getLocale();
            if($locale !== $_locale){//Si la locale n'est pas la langue sur laquelle l'utilisateur souhaite naviguer, on la modifie
                $request->getSession()->set('_locale', $_locale);
            }
        }else{//On utilise la langue par défaut si aucune locale n'est précisée
            $langue = $repoLangue->findOneBy(array('defaut' => true));

            $locale = $request->getLocale();
            if($locale !== $langue->getAbreviation()){//Si la locale n'est pas la langue par défaut, on la modifie
                $request->getSession()->set('_locale', $langue->getAbreviation());
            }
        }

        $page = $langue->getPageAccueil();

        return $this->render('front/accueil.html.twig', array('page'=>$page, 'accueil'=>$accueil));
    }
}