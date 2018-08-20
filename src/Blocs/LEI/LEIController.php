<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 20/08/2018
 * Time: 14:22
 */

namespace App\Blocs\LEI;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LEIController extends Controller
{
    /**
     * @Route("/fiche/{idFiche}/{url}", name="voirFicheLEI")
     * @param Request $request
     * @param $idFiche
     * @param $url
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function voirFicheLEIAction($idFiche, $url){
        $xml = simplexml_load_file('https://apps.tourisme-alsace.info/batchs/LIENS_PERMANENTS/2002206000029_Batch_siteweb_terres_oh.xml');

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