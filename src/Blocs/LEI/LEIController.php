<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 20/08/2018
 * Time: 14:22
 */

namespace App\Blocs\LEI;


use App\Entity\Bloc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LEIController extends Controller
{
    /**
     * @Route("/{_locale}/fiche/{url}/{idFiche}/{idBloc}", name="voirFicheLEI")
     * @param Request $request
     * @param $idFiche
     * @param $url
     * @param $idModule
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function voirFicheLEIAction($url, $idFiche, $idBloc){
        $repoBloc = $this->getDoctrine()->getRepository(Bloc::class);
        $bloc = $repoBloc->find($idBloc);
        $flux = $bloc->getContenu()['flux'];

        $xml = simplexml_load_file($flux);

        $json = json_encode($xml);

        $php = json_decode($json);

        $fiches = $php->Resultat->sit_liste;

        $ficheRecherchee = null;
        foreach($fiches as $fiche){
            if($fiche->PRODUIT == $idFiche){
                $ficheRecherchee = $fiche;
                break;
            }
        }

        return $this->render('Blocs/LEI/fiche.html.twig', array('fiche'=>$ficheRecherchee));
    }
}