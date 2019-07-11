<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 20/08/2018
 * Time: 14:22
 */

namespace App\Blocs\LEI;


use App\Entity\Bloc;
use App\Service\Langue;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class LEIController extends Controller
{
    /**
     * @Route("/fiche/{url}/{idFiche}/{idBloc}", name="voirFicheLEI")
     * @Route("/{_locale}/fiche/{url}/{idFiche}/{idBloc}", name="voirFicheLEILocale")
     * @param Request $request
     * @param $idFiche
     * @param $url
     * @param $idModule
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function voirFicheLEIAction($url, $idFiche, $idBloc, Langue $slangue, $_locale = null){
        //Test route : locale ou non
        $redirection = $slangue->redirectionLocale('voirFicheLEI', $_locale, array('url' => $url, 'idFiche' => $idFiche, 'idBloc' => $idBloc));
        if($redirection){
            return $redirection;
        }

        $repoBloc = $this->getDoctrine()->getRepository(Bloc::class);
        $bloc = $repoBloc->find($idBloc);
        $flux = $bloc->getContenu()['flux'];

        $xml = simplexml_load_file($flux);

        $fiche = $xml->xpath("//Resultat/sit_liste[PRODUIT = $idFiche]");

        if(!$fiche){
            throw new NotFoundHttpException('Cette page n\'existe pas ou a été supprimée');
        }

        return $this->render('Blocs/LEI/fiche.html.twig', array('fiche'=>$fiche[0]));
    }
}