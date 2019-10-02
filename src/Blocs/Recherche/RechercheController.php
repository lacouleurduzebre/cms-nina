<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/12/2018
 * Time: 13:48
 */

namespace App\Blocs\Recherche;


use App\Entity\Bloc;
use App\Entity\Page;
use App\Service\Langue;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RechercheController extends Controller
{
    /**
     * @Route("/recherche/resultats", name="recherche")
     * @Route("/{_locale}/recherche/resultats", name="rechercheLocale", requirements={
     *     "_locale"="^[A-Za-z]{1,2}$"
     * })
     */
    public function rechercheAction(Request $request, Langue $slangue, $_locale = null){
        //Test route : locale ou non
        $redirection = $slangue->redirectionLocale('recherche', $_locale);
        if($redirection){
            return $redirection;
        }

        $recherche = $request->get('recherche');

        $motsCles = explode(' ', $recherche);

        $repoPage = $this->getDoctrine()->getRepository(Page::class);
        $pages = $repoPage->recherche($motsCles);

        $repoBlocs = $this->getDoctrine()->getRepository(Bloc::class);
        $blocs = $repoBlocs->recherche($motsCles);

        $pages = array_merge($pages, $blocs);

        return $this->render('Blocs/Recherche/ResultatsRecherche.html.twig', array('pages' => $pages));
    }
}