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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RechercheController extends Controller
{
    /**
     * @Route("/{_locale}/recherche", name="recherche")
     */
    public function rechercheAction(Request $request){
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