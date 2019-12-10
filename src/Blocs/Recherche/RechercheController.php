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
use App\Service\Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RechercheController extends AbstractController
{
    /**
     * @Route("/recherche/resultats", name="recherche")
     * @Route("/{_locale}/recherche/resultats", name="rechercheLocale", requirements={
     *     "_locale"="^[A-Za-z]{1,2}$"
     * })
     */
    public function rechercheAction(Request $request, Langue $slangue, Pagination $pagination, $_locale = null){
        //Test route : locale ou non
        $redirection = $slangue->redirectionLocale('recherche', $_locale);
        if($redirection){
            return $redirection;
        }

        $recherche = $request->get('recherche');

        $motsCles = explode(' ', $recherche);

        $repoPage = $this->getDoctrine()->getRepository(Page::class);
        $pages = $repoPage->recherche($motsCles);

        //Poids des pages dont un des mots-clés est dans le titre
        foreach($pages as $page){
            $page->poids = 999;
        }
        //Poids des pages dont un des mots-clés est dans le titre

        $repoBloc = $this->getDoctrine()->getRepository(Bloc::class);
        $blocs = $repoBloc->recherche($motsCles);

        $pages = array_merge($pages, $blocs);

        //Poids des autres pages
        foreach($pages as $page){
            if(!isset($page->poids)){
                $tpl = $this->renderView('front/blocs.html.twig', ['blocs' => $page->getBlocs()]);
                $tpl = strip_tags($tpl);

                $poids = 0;

                foreach($motsCles as $motCle){
                    $poids += substr_count($tpl, $motCle);
                }

                $page->poids = $poids;
            }
        }
        //Poids des autres pages

        //Tri par poids
        usort($pages, function ($a, $b) {
            if ($a->poids == $b->poids) {
                return 0;
            }

            return ($a->poids > $b->poids) ? -1 : 1;
        });
        //Tri par poids

        //Pagination
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $parametres = [
            'pagination' => [
                1
            ],
            'resultatsParPage' => 10
        ];
        $resultats = $pagination->getPagination($pages, $parametres, $page);
        //Pagination

        //Résumé des pages
        foreach($resultats['donnees'] as $page){
            $tpl = $this->renderView('front/blocs.html.twig', ['blocs' => $page->getBlocs()]);
            $tpl = strip_tags($tpl);
            $resume = substr($tpl, 0, 300).'...';

            foreach($motsCles as $motCle){
                $resume = str_ireplace($motCle, '<strong>'.$motCle.'</strong>', $resume);
                $page->setTitre(str_ireplace($motCle, '<strong>'.$motCle.'</strong>', $page->getTitre()));
            }

            $page->resume = $resume;
        }
        //Résumé des pages

        return $this->render('Blocs/Recherche/ResultatsRecherche.html.twig', array('resultats' => $resultats));
    }
}